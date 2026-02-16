<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use Livewire\Component;

class TicketHistorial extends Component
{
    public Ticket $ticket;

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-historial', [
            'historial' => $this->ticket->historial()
                ->with('usuarioHistorial')
                ->latest()
                ->get()
        ]);
    }
}
