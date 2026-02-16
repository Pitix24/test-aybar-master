<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketHistorial;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketParticipante extends Component
{
    public Ticket $ticket;
    public $searchUser = '';

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function addParticipant($userId)
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        try {
            DB::beginTransaction();

            if (!$this->ticket->usuariosParticipantes()->where('users.id', $userId)->exists()) {
                $this->ticket->usuariosParticipantes()->attach($userId);

                $user = User::find($userId);
                TicketHistorial::create([
                    'ticket_id' => $this->ticket->id,
                    'user_id' => auth()->id(),
                    'accion' => 'Agregar Participante',
                    'detalle' => "Se agregó al usuario: {$user->name}",
                ]);

                $this->dispatch('alertaLivewire', ['title' => 'Agregado', 'text' => 'Participante añadido correctamente.']);
            }

            DB::commit();

            $this->searchUser = '';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error TicketParticipante@addParticipant: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo agregar al participante.']);
        }
    }

    public function removeParticipant($userId)
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        try {
            DB::beginTransaction();

            $this->ticket->usuariosParticipantes()->detach($userId);

            $user = User::find($userId);
            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Quitar Participante',
                'detalle' => "Se retiró al usuario: {$user->name}",
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Quitado', 'text' => 'Participante retirado.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error TicketParticipante@removeParticipant: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo retirar al participante.']);
        }
    }

    public function render()
    {
        $participantesSeleccionados = $this->ticket->usuariosParticipantes()->get();

        $participantesDisponibles = [];
        if (strlen($this->searchUser) > 2) {
            $participantesDisponibles = User::where('activo', true)
                ->where('name', 'like', "%{$this->searchUser}%")
                ->whereNotIn('id', $participantesSeleccionados->pluck('id'))
                ->limit(5)
                ->get();
        }

        return view('livewire.erp.atc.ticket.ticket-participante', [
            'participantesSeleccionados' => $participantesSeleccionados,
            'participantesDisponibles' => $participantesDisponibles
        ]);
    }
}
