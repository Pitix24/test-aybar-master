<?php

namespace App\Livewire\Erp\Negocio\Area;

use App\Models\Area;
use App\Models\Sede;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Área')]
class AreaVer extends Component
{
    public Area $area_model;

    public function mount($id)
    {
        $this->area_model = Area::with('sedes')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.negocio.area.area-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
