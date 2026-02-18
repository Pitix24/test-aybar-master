<?php

namespace App\Livewire\Erp\Atc\EstadoTicket;

use App\Models\EstadoTicket;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Estado de Ticket')]
class EstadoTicketVer extends Component
{
    public EstadoTicket $estado;

    public function mount($id)
    {
        $this->estado = EstadoTicket::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.atc.estado-ticket.estado-ticket-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
