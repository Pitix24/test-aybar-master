<?php

namespace App\Livewire\Erp\Reporte\PowerBI;

use App\Services\PowerBiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte Solicitud Evidencia de Pago — Power BI')]
class ReporteSolicitudEvidenciaPagoPowerBI extends Component
{
    public array $embedData = [];
    public string $titulo = 'Reporte Solicitud Evidencia de Pago';
    public string $reporteKey = 'solicitud-evidencia-pago';
    public string $rutaClasica = '';

    public function mount(PowerBiService $powerBiService)
    {
        $this->embedData = $powerBiService->getEmbedData($this->reporteKey);
        $this->rutaClasica = route('reporte.vista.solicitud-evidencia-pago');
    }

    public function render()
    {
        return view('livewire.erp.reporte.powerbi.reporte-solicitud-evidencia-pago-powerbi');
    }
}
