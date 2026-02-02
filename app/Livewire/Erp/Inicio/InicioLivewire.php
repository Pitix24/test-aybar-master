<?php

namespace App\Livewire\Erp\Inicio;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.erp.layout-erp')]
class InicioLivewire extends Component
{
    public function render()
    {
        return view('livewire.erp.inicio.inicio-livewire');
    }
}
