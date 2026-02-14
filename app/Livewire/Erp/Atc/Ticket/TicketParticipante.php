<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketHistorial;
use Livewire\Component;

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

        $this->searchUser = '';
    }

    public function removeParticipant($userId)
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        $this->ticket->usuariosParticipantes()->detach($userId);

        $user = User::find($userId);
        TicketHistorial::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => auth()->id(),
            'accion' => 'Quitar Participante',
            'detalle' => "Se retiró al usuario: {$user->name}",
        ]);

        $this->dispatch('alertaLivewire', ['title' => 'Quitado', 'text' => 'Participante retirado.']);
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
