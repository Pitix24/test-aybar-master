<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
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

    public function mount($id)
    {
        $this->ticket = Ticket::with(['hijos', 'padre.gestor', 'usuariosParticipantes', 'cliente'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
