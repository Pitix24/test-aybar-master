<?php

namespace App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Estado de Evidencia de Pago')]
class EstadoSolicitudEvidenciaPagoVer extends Component
{
    public EstadoSolicitudEvidenciaPago $estado;

    public function mount($id)
    {
        $this->authorize('estado-solicitud-evidencia-pago.ver');
        $this->estado = EstadoSolicitudEvidenciaPago::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.backoffice.estado-solicitud-evidencia-pago.estado-solicitud-evidencia-pago-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
