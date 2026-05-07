<?php

namespace App\Livewire\Erp\Soporte\TipoSoporte;

use App\Models\Erp\Soporte\TipoSoporte;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Tipo de Soporte')]
class TipoSoporteVer extends Component
{
    public TipoSoporte $tipo;

    public function mount($id)
    {
        $this->tipo = TipoSoporte::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.soporte.tipo-soporte.tipo-soporte-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
