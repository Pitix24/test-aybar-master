<?php

namespace App\Livewire\Erp\EntregaFest\Staff;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Recursos y Protocolos - Entrega Fest')]
class StaffRecursos extends Component
{
    public EntregaFest $evento;
    public $tab = 'MAPAS';

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['recursos', 'protocolos', 'contingencias'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.staff.staff-recursos');
    }
}