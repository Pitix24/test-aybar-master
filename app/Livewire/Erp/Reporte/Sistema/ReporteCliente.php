<?php

namespace App\Livewire\Erp\Reporte\Sistema;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.erp.layout-erp')]
#[Title('Reporte Cliente')]
class ReporteCliente extends Component
{
    public function render()
    {
        return view('livewire.erp.reporte.sistema.reporte-cliente');
    }
}
