<?php

namespace App\Livewire\Erp\Negocio\UnidadNegocio;

use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Unidad de Negocio')]
class UnidadNegocioVer extends Component
{
    public UnidadNegocio $unidad_model;

    public function mount($id)
    {
        $this->unidad_model = UnidadNegocio::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.negocio.unidad-negocio.unidad-negocio-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
