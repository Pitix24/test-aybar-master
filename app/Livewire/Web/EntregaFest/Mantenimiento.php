<?php

namespace App\Livewire\Web\EntregaFest;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Mantenimiento Temporal - Entrega Fest')]
class Mantenimiento extends Component
{
    public function render()
    {
        return view('livewire.web.entrega-fest.mantenimiento');
    }
}
