<?php

namespace App\Livewire\Erp\Atc\PrioridadTicket;

use App\Models\PrioridadTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Prioridad de Ticket')]
class PrioridadTicketEditar extends Component
{
    public PrioridadTicket $prioridad_model;

    public $nombre;
    public $tiempo_permitido;
    public $color;
    public $icono;
    public $activo;

    public function mount($id)
    {
        $this->authorize('prioridad-ticket.editar');
        $this->prioridad_model = PrioridadTicket::findOrFail($id);

        $this->nombre = $this->prioridad_model->nombre;
        $this->tiempo_permitido = $this->prioridad_model->tiempo_permitido;
        $this->color = $this->prioridad_model->color;
        $this->icono = $this->prioridad_model->icono;
        $this->activo = (bool) $this->prioridad_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:prioridad_tickets,nombre,' . $this->prioridad_model->id,
            'tiempo_permitido' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre de la prioridad',
            'tiempo_permitido' => 'tiempo permitido (horas)',
            'color' => 'color informativo',
            'icono' => 'icono representativo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('prioridad-ticket.editar');

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

            $this->prioridad_model->update([
                'nombre' => trim($this->nombre),
                'tiempo_permitido' => $this->tiempo_permitido,
                'color' => $this->color ?? '#3b82f6',
                'icono' => $this->icono ?? 'fa-solid fa-flag',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'La prioridad de ticket se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('prioridad_ticket')->error("[PRIORIDAD TICKET] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->prioridad_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la prioridad de ticket.'
            ]);
        }
    }

    #[On('eliminarPrioridadTicketOn')]
    public function eliminarPrioridadTicketOn()
    {
        $this->authorize('prioridad-ticket.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->prioridad_model->nombre;
            $this->prioridad_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "La prioridad de ticket '$nombre' ha sido eliminada."
            ]);

            return redirect()->route('erp.prioridad-ticket.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('prioridad_ticket')->error("[PRIORIDAD TICKET] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->prioridad_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la prioridad de ticket.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.prioridad-ticket.prioridad-ticket-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
