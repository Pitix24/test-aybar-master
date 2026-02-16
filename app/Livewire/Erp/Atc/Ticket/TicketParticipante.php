<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use Livewire\Component;

class TicketParticipante extends Component
{
    public Ticket $ticket;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function render()
    {
        $participantesSeleccionados = $this->ticket->usuariosParticipantes()->get();

        return view('livewire.erp.atc.ticket.ticket-participante', [
            'participantesSeleccionados' => $participantesSeleccionados,
        ]);
    }
}
