<?php

namespace App\Livewire\Erp\Reporte\Sistema;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
#[Layout('layouts.erp.layout-erp')]
#[Title('Reporte Solicitud Evidencia Pago')]
class ReporteSolicitudEvidenciaPago extends Component
{
    public function render()
    {
        return view('livewire.erp.reporte.sistema.reporte-solicitud-evidencia-pago');
    }
}
