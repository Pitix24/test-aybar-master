<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use Livewire\Component;

class TicketDerivados extends Component
{
    public Ticket $ticket;

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-derivados', [
            'derivados' => $this->ticket->derivados()
                ->with(['deArea', 'aArea', 'usuarioDeriva', 'usuarioRecibe'])
                ->latest()
                ->get()
        ]);
    }
}
