<?php

namespace App\Livewire\Erp\Soporte\PrioridadSoporte;

use App\Models\Erp\Soporte\PrioridadSoporte;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Prioridad de Soporte')]
class PrioridadSoporteVer extends Component
{
    public PrioridadSoporte $prioridad;

    public function mount($id)
    {
        $this->prioridad = PrioridadSoporte::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.soporte.prioridad-soporte.prioridad-soporte-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
