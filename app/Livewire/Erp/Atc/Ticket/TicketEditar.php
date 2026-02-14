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

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
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

    public function update()
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        $this->validate();

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

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Cambios guardados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TicketEditar@update: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudieron guardar los cambios.']);
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        abort_unless(auth()->user()->can('ticket.eliminar'), 403);

        try {
            if ($this->ticket->hijos()->exists()) {
                $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Este ticket tiene hijos, no se puede eliminar.']);
                return;
            }

            $this->ticket->delete();
            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            Log::error('Error TicketEditar@eliminarTicketOn: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-editar', [
            'estados' => EstadoTicket::where('activo', true)->get(),
            'historial' => $this->ticket->historial()->with('usuarioHistorial')->latest()->get(),
            'derivados' => $this->ticket->derivados()->with(['deArea', 'aArea', 'usuarioDeriva', 'usuarioRecibe'])->latest()->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
