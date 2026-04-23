<?php

namespace App\Livewire\Erp\Reporte\PowerBI;

use App\Services\PowerBiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Clientes — Power BI')]
class ReporteClientePowerBI extends Component
{
    public array $embedData = [];
    public string $titulo = 'Reporte de Clientes';
    public string $reporteKey = 'cliente';
    public string $rutaClasica = '';

    public function mount(PowerBiService $powerBiService)
    {
        $this->embedData = $powerBiService->getEmbedData($this->reporteKey);
        $this->rutaClasica = route('erp.reporte.vista.cliente');
    }

    public function render()
    {
        return view('livewire.erp.reporte.powerbi.reporte-cliente-powerbi');
    }
}
