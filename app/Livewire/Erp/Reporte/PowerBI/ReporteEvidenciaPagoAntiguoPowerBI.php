<?php

namespace App\Livewire\Erp\Reporte\PowerBI;

use App\Services\PowerBiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte Evidencia de Pago Antiguo — Power BI')]
class ReporteEvidenciaPagoAntiguoPowerBI extends Component
{
    public array $embedData = [];
    public string $titulo = 'Reporte Evidencia de Pago Antiguo';
    public string $reporteKey = 'evidencia-pago-antiguo';
    public string $rutaClasica = '';

    public function mount(PowerBiService $powerBiService)
    {
        $this->embedData = $powerBiService->getEmbedData($this->reporteKey);
        $this->rutaClasica = route('reporte.vista.evidencia-pago-antiguo');
    }

    public function render()
    {
        return view('livewire.erp.reporte.powerbi.reporte-evidencia-pago-antiguo-powerbi');
    }
}
