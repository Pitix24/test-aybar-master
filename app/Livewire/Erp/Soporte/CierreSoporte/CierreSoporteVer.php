<?php

namespace App\Livewire\Erp\Soporte\CierreSoporte;

use App\Models\Erp\Soporte\CierreSoporte;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Cierre de Soporte')]
class CierreSoporteVer extends Component
{
    public CierreSoporte $cierre;

    public function mount($id)
    {
        $this->cierre = CierreSoporte::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.soporte.cierre-soporte.cierre-soporte-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
