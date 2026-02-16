<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\EstadoTicket;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Ver Ticket')]
class TicketVer extends Component
{
    public Ticket $ticket;

    public $email;
    public $celular;
    public $estado_ticket_id;

    public function mount($id)
    {
        $this->authorize('ticket.ver');
        $this->ticket = Ticket::with(['hijos', 'padre.gestor', 'usuariosParticipantes', 'cliente'])->findOrFail($id);

        $this->email = $this->ticket->email;
        $this->celular = $this->ticket->celular;
        $this->estado_ticket_id = $this->ticket->estado_ticket_id;
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-ver', [
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
