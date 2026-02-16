<?php

namespace App\Livewire\Erp\Atc\PrioridadTicket;

use App\Models\PrioridadTicket;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Prioridad de Ticket')]
class PrioridadTicketVer extends Component
{
    public PrioridadTicket $prioridad;

    public function mount($id)
    {
        $this->authorize('prioridad-ticket.ver');
        $this->prioridad = PrioridadTicket::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.atc.prioridad-ticket.prioridad-ticket-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
