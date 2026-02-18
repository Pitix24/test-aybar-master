<?php

namespace App\Livewire\Erp\Reporte\Backoffice;

use App\Models\EvidenciaPago;
use App\Models\EstadoSolicitudEvidenciaPago;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Análisis de Evidencias Técnicas')]
class ReporteEvidenciaPago extends Component
{
    // KPIs
    public $totalArchivos;
    public $cierreManual;
    public $cierreSlin;
    public $montoTotalProcesado;
    public $tasaAutomatizacion;

    // Gráficos
    public $metodoCierre = [];
    public $distribucionBancos = [];
    public $evolucionMontos = [];
    public $porExtension = [];
    public $porEstado = [];

    // Tablas
    public $ultimasEvidencias = [];

    // Filtros
    public $mesSeleccionado;

    public function mount()
    {
        $this->mesSeleccionado = Carbon::now()->format('Y-m');
        $this->cargarDataGlobal();
        $this->actualizarGraficosMensuales();
        $this->cargarTablasRecientes();
    }

    private function cargarDataGlobal()
    {
        $this->totalArchivos = EvidenciaPago::count();
        $this->cierreSlin = EvidenciaPago::whereNotNull('slin_respuesta')->count();
        $this->cierreManual = EvidenciaPago::whereNull('slin_respuesta')->count();
        $this->montoTotalProcesado = EvidenciaPago::sum('monto');

        $this->tasaAutomatizacion = $this->totalArchivos > 0
            ? round(($this->cierreSlin / $this->totalArchivos) * 100, 1)
            : 0;

        $this->cargarDistribucionEstatica();
    }

    public function updatedMesSeleccionado()
    {
        $this->actualizarGraficosMensuales();

        $this->dispatch('actualizarGraficosDinamicos', [
            'montos' => $this->evolucionMontos,
            'metodos' => $this->metodoCierre
        ]);
    }

    private function actualizarGraficosMensuales()
    {
        $this->cargarMetodoCierre();
        $this->cargarEvolucionMontos();
    }

    private function cargarDistribucionEstatica()
    {
        // Bancos
        $dataBancos = EvidenciaPago::select('banco', DB::raw('count(*) as total'))
            ->whereNotNull('banco')
            ->groupBy('banco')
            ->orderByDesc('total')
            ->get();
        $this->distribucionBancos = [
            'labels' => $dataBancos->pluck('banco')->toArray(),
            'data' => $dataBancos->pluck('total')->toArray(),
        ];

        // Extensiones
        $dataExt = EvidenciaPago::select('extension', DB::raw('count(*) as total'))
            ->groupBy('extension')
            ->get();
        $this->porExtension = [
            'labels' => $dataExt->pluck('extension')->toArray(),
            'data' => $dataExt->pluck('total')->toArray(),
        ];

        // Estados
        $dataEstado = EvidenciaPago::select('estado_solicitud_evidencia_pago_id', DB::raw('count(*) as total'))
            ->groupBy('estado_solicitud_evidencia_pago_id')
            ->with('estado')
            ->get();
        $this->porEstado = [
            'labels' => $dataEstado->map(fn($i) => $i->estado?->nombre ?? 'Pendiente')->toArray(),
            'data' => $dataEstado->pluck('total')->toArray(),
        ];
    }

    private function cargarMetodoCierre()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);

        $slin = EvidenciaPago::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNotNull('slin_respuesta')
            ->count();

        $manual = EvidenciaPago::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereNull('slin_respuesta')
            ->count();

        $this->metodoCierre = [
            'labels' => ['Cierre SLIN (Automático)', 'Cierre Manual'],
            'data' => [$slin, $manual],
        ];
    }

    private function cargarEvolucionMontos()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $data = EvidenciaPago::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as dia, SUM(monto) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $this->evolucionMontos = [
            'labels' => array_values(range(1, $diasDelMes)),
            'data' => array_values(collect(range(1, $diasDelMes))->map(fn($d) => $data[$d] ?? 0)->toArray()),
        ];
    }

    private function cargarTablasRecientes()
    {
        $this->ultimasEvidencias = EvidenciaPago::with(['solicitud', 'estado'])
            ->latest()
            ->take(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.erp.reporte.backoffice.reporte-evidencia-pago');
    }
}
