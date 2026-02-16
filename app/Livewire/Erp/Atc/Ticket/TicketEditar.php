<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Models\EstadoTicket;
use App\Models\TicketHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Ticket')]
class TicketEditar extends Component
{
    public Ticket $ticket;

    // Campos editables
    public $email;
    public $celular;
    public $estado_ticket_id;

    // Catálogos y datos para UI
    public $mapEstados = [];

    protected function rules()
    {
        return [
            'email' => 'nullable|email|max:150',
            'celular' => 'nullable|string|max:50',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::with(['hijos', 'padre.gestor', 'usuariosParticipantes', 'cliente'])->findOrFail($id);

        $this->email = $this->ticket->email;
        $this->celular = $this->ticket->celular;
        $this->estado_ticket_id = $this->ticket->estado_ticket_id;

        $this->mapEstados = EstadoTicket::pluck('nombre', 'id')->toArray();
    }

    public function validationAttributes()
    {
        return [
            'email' => 'Correo Electrónico',
            'celular' => 'Número de Celular',
            'estado_ticket_id' => 'Estado del Ticket',
        ];
    }

    public function update()
    {
        $this->authorize('ticket.editar');

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

            $old = $this->ticket->fresh();
            $cambios = [];

            if ($this->estado_ticket_id != $old->estado_ticket_id) {
                $viejo = $this->mapEstados[$old->estado_ticket_id] ?? 'N/A';
                $nuevo = $this->mapEstados[$this->estado_ticket_id] ?? 'N/A';
                $cambios[] = "Estado cambiado de '$viejo' a '$nuevo'";
            }

            if ($this->email != $old->email) {
                $cambios[] = "Correo actualizado de '" . ($old->email ?? 'vacío') . "' a '{$this->email}'";
            }

            if ($this->celular != $old->celular) {
                $cambios[] = "Celular actualizado de '" . ($old->celular ?? 'vacío') . "' a '{$this->celular}'";
            }

            $this->ticket->update([
                'email' => $this->email,
                'celular' => $this->celular,
                'estado_ticket_id' => $this->estado_ticket_id,
                'updated_by' => auth()->id(),
            ]);

            if (!empty($cambios)) {
                TicketHistorial::create([
                    'ticket_id' => $this->ticket->id,
                    'user_id' => auth()->id(),
                    'accion' => 'Edición',
                    'detalle' => implode(" | ", $cambios),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'Cambios guardados correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error en Edición: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'ticket_id' => $this->ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudieron guardar los cambios. Intente nuevamente.'
            ]);
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        $this->authorize('ticket.eliminar');

        try {
            if ($this->ticket->hijos()->exists()) {
                $this->dispatch('alertaLivewire', [
                    'type' => 'warning',
                    'title' => 'No permitido',
                    'text' => 'Este ticket tiene tickets hijos asociados y no puede ser eliminado.'
                ]);
                return;
            }

            $ticket_id = $this->ticket->id;
            $this->ticket->delete();

            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            Log::channel('ticket')->error('[TICKET] Error en Eliminación: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'ticket_id' => $this->ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el ticket.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-editar', [
            'estados' => EstadoTicket::where('activo', true)->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
