<?php

namespace App\Livewire\Erp\Soporte\EstadoSoporte;

use App\Models\Erp\Soporte\EstadoSoporte;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Estado de Soporte')]
class EstadoSoporteVer extends Component
{
    public EstadoSoporte $estado;

    public function mount($id)
    {
        $this->estado = EstadoSoporte::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.soporte.estado-soporte.estado-soporte-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
