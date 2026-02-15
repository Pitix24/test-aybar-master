<?php

namespace App\Livewire\Erp\Negocio\Sede;

use App\Models\Sede;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Sede')]
class SedeVer extends Component
{
    public Sede $sede_model;

    public function mount($id)
    {
        $this->authorize('sede.ver');
        $this->sede_model = Sede::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.negocio.sede.sede-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
