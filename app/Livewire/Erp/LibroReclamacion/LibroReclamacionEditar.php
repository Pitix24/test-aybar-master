<?php

namespace App\Livewire\Erp\LibroReclamacion;

use App\Models\LibroReclamacion\TicketLibroReclamacion;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Ticket Libro Reclamacion')]
class LibroReclamacionEditar extends Component
{
    public TicketLibroReclamacion $ticket_model;

    public $codigo = '';
    public $libro_reclamacion_ticket = '';
    public $unidad_negocio_id = '';
    public $proyecto_id = '';
    public $cliente_id = '';
    public $gestor_id = '';
    public $estado_legal = 'NUEVO';
    public $clasificacion = 'PENDIENTE_REVISION';
    public $nota_fuente = '';
    public $observaciones_internas = '';

    public $unidades = [];
    public $proyectos = [];
    public $usuarios = [];

    public function mount($id): void
    {
        $this->authorize('ticket-libro-reclamacion.editar');

        $this->ticket_model = TicketLibroReclamacion::findOrFail($id);

        $this->codigo = $this->ticket_model->codigo;
        $this->libro_reclamacion_ticket = $this->ticket_model->libro_reclamacion_ticket;
        $this->unidad_negocio_id = $this->ticket_model->unidad_negocio_id;
        $this->proyecto_id = $this->ticket_model->proyecto_id;
        $this->cliente_id = $this->ticket_model->cliente_id;
        $this->gestor_id = $this->ticket_model->gestor_id;
        $this->estado_legal = $this->ticket_model->estado_legal;
        $this->clasificacion = $this->ticket_model->clasificacion;
        $this->nota_fuente = $this->ticket_model->nota_fuente;
        $this->observaciones_internas = $this->ticket_model->observaciones_internas;

        $this->unidades = UnidadNegocio::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $this->proyectos = Proyecto::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $this->usuarios = User::query()->where('activo', true)->orderBy('name')->get(['id', 'name']);
    }

    protected function rules(): array
    {
        return [
            'unidad_negocio_id' => 'nullable|exists:unidad_negocios,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'cliente_id' => 'nullable|exists:users,id',
            'gestor_id' => 'nullable|exists:users,id',
            'estado_legal' => 'required|in:NUEVO,EN_GESTION,OBSERVADO,RESUELTO,NO_PROCEDE,CERRADO',
            'clasificacion' => 'required|in:PROCEDE,NO_PROCEDE,PENDIENTE_REVISION',
            'nota_fuente' => 'nullable|string',
            'observaciones_internas' => 'nullable|string',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function update(): void
    {
        $this->authorize('ticket-libro-reclamacion.editar');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $asignadoAntes = $this->ticket_model->gestor_id;
            $asignadoNuevo = $this->gestor_id ?: null;

            $this->ticket_model->update([
                'unidad_negocio_id' => $this->unidad_negocio_id ?: null,
                'proyecto_id' => $this->proyecto_id ?: null,
                'cliente_id' => $this->cliente_id ?: null,
                'gestor_id' => $asignadoNuevo,
                'estado_legal' => $this->estado_legal,
                'clasificacion' => $this->clasificacion,
                'nota_fuente' => $this->textoNullable($this->nota_fuente),
                'observaciones_internas' => $this->textoNullable($this->observaciones_internas),
                'assigned_at' => $this->resolverAssignedAt($asignadoAntes, $asignadoNuevo),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'El ticket de libro de reclamacion se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[TICKET-LIBRO] Error al actualizar: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->ticket_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el ticket de libro de reclamacion.'
            ]);
        }
    }

    #[On('eliminarLibroTicketOn')]
    public function eliminarLibroTicketOn()
    {
        $this->authorize('ticket-libro-reclamacion.eliminar');

        try {
            DB::beginTransaction();

            $codigo = $this->ticket_model->codigo;
            $this->ticket_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => "El ticket $codigo fue eliminado correctamente."
            ]);

            return redirect()->route('erp.libro-reclamacion.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[TICKET-LIBRO] Error al eliminar: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->ticket_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el ticket de libro de reclamacion.'
            ]);
        }
    }

    protected function resolverAssignedAt(?int $antes, ?int $nuevo)
    {
        if ($nuevo && $antes !== $nuevo) {
            return now();
        }

        return $this->ticket_model->assigned_at;
    }

    protected function textoNullable(?string $valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    public function render()
    {
        return view('livewire.erp.libro-reclamacion.libro-reclamacion-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
