<?php

namespace App\Livewire\Erp\Reporte\PowerBI;

use App\Services\PowerBiService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Tickets — Power BI')]
class ReporteTicketPowerBI extends Component
{
    public array $embedData = [];
    public string $titulo = 'Reporte de Tickets';
    public string $reporteKey = 'ticket';
    public string $rutaClasica = '';

    public function mount(PowerBiService $powerBiService)
    {
        $this->embedData = $powerBiService->getEmbedData($this->reporteKey);
        $this->rutaClasica = route('reporte.vista.ticket');
    }

    public function render()
    {
        return view('livewire.erp.reporte.powerbi.reporte-ticket-powerbi');
    }
}
