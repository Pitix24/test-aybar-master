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
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Ticket Libro Reclamacion')]
class LibroReclamacionCrear extends Component
{
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

    public function mount(): void
    {
        $this->authorize('ticket-libro-reclamacion.crear');

        $this->codigo = TicketLibroReclamacion::generarCodigo();
        $this->unidades = UnidadNegocio::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $this->proyectos = Proyecto::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);
        $this->usuarios = User::query()->where('activo', true)->orderBy('name')->get(['id', 'name']);
    }

    protected function rules(): array
    {
        return [
            'codigo' => 'required|string|max:30|unique:ticket_libro_reclamacions,codigo',
            'libro_reclamacion_ticket' => 'nullable|exists:libro_reclamacions,ticket',
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

    public function store()
    {
        $this->authorize('ticket-libro-reclamacion.crear');

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

            TicketLibroReclamacion::create([
                'codigo' => trim($this->codigo),
                'libro_reclamacion_ticket' => $this->libro_reclamacion_ticket ?: null,
                'unidad_negocio_id' => $this->unidad_negocio_id ?: null,
                'proyecto_id' => $this->proyecto_id ?: null,
                'cliente_id' => $this->cliente_id ?: null,
                'gestor_id' => $this->gestor_id ?: null,
                'estado_legal' => $this->estado_legal,
                'clasificacion' => $this->clasificacion,
                'nota_fuente' => $this->textoNullable($this->nota_fuente),
                'observaciones_internas' => $this->textoNullable($this->observaciones_internas),
                'assigned_at' => $this->gestor_id ? now() : null,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Creado',
                'text' => 'El ticket de libro de reclamacion se creó correctamente.'
            ]);

            return redirect()->route('erp.libro-reclamacion.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[TICKET-LIBRO] Error al crear: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el ticket de libro de reclamacion.'
            ]);
        }
    }

    protected function textoNullable(?string $valor): ?string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    public function render()
    {
        return view('livewire.erp.libro-reclamacion.libro-reclamacion-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
