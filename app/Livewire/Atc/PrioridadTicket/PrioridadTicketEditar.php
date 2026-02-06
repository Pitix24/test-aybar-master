<?php

namespace App\Livewire\Atc\PrioridadTicket;

use App\Models\PrioridadTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class PrioridadTicketEditar extends Component
{
    public PrioridadTicket $prioridadTicket;

    public $nombre;
    public $tiempo_permitido;
    public $color;
    public $icono;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:prioridad_tickets,nombre,' . $this->prioridadTicket->id,
            'tiempo_permitido' => 'required|numeric|min:0',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->prioridadTicket = PrioridadTicket::findOrFail($id);

        $this->nombre = $this->prioridadTicket->nombre;
        $this->tiempo_permitido = $this->prioridadTicket->tiempo_permitido;
        $this->color = $this->prioridadTicket->color;
        $this->icono = $this->prioridadTicket->icono;
        $this->activo = $this->prioridadTicket->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->prioridadTicket->update([
                'nombre' => $this->nombre,
                'tiempo_permitido' => $this->tiempo_permitido,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar prioridad de ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarPrioridadTicketOn')]
    public function eliminarPrioridadTicketOn()
    {
        try {
            DB::beginTransaction();

            $this->prioridadTicket->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.prioridad-ticket.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar prioridad de ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.atc.prioridad-ticket.prioridad-ticket-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
