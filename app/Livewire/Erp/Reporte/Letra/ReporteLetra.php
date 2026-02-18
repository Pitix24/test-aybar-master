<?php

namespace App\Livewire\Erp\Reporte\Letra;

use App\Models\SolicitudDigitalizarLetra;
use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Digitalización de Letras')]
class ReporteLetra extends Component
{
    // KPIs
    public $totalSolicitudes;
    public $totalImporte;
    public $solicitudesAprobadas;
    public $solicitudesPendientes;
    public $tasaAprobacion;

    // Gráficos
    public $porEstado = [];
    public $porUnidadNegocio = [];
    public $porProyecto = [];
    public $tendenciaRegistro = [];
    public $distribucionImportes = [];

    // Tablas
    public $ultimasSolicitudes = [];

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
        $this->totalSolicitudes = SolicitudDigitalizarLetra::count();
        $this->totalImporte = SolicitudDigitalizarLetra::sum(DB::raw('CAST(importe_cuota AS DECIMAL(10,2))'));

        $this->solicitudesAprobadas = SolicitudDigitalizarLetra::whereHas('estado', function ($q) {
            $q->where('nombre', EstadoSolicitudDigitalizarLetra::APROBADO);
        })->count();

        $this->solicitudesPendientes = SolicitudDigitalizarLetra::whereHas('estado', function ($q) {
            $q->where('nombre', EstadoSolicitudDigitalizarLetra::PENDIENTE);
        })->count();

        $this->tasaAprobacion = $this->totalSolicitudes > 0
            ? round(($this->solicitudesAprobadas / $this->totalSolicitudes) * 100, 1)
            : 0;

        $this->cargarDistribucionEstatica();
    }

    public function updatedMesSeleccionado()
    {
        $this->actualizarGraficosMensuales();

        $this->dispatch('actualizarGraficosDinamicos', [
            'tendencia' => $this->tendenciaRegistro
        ]);
    }

    private function actualizarGraficosMensuales()
    {
        $this->cargarTendenciaRegistro();
    }

    private function cargarDistribucionEstatica()
    {
        // Por Estado
        $dataEstado = SolicitudDigitalizarLetra::select('estado_solicitud_digitalizar_letra_id', DB::raw('count(*) as total'))
            ->groupBy('estado_solicitud_digitalizar_letra_id')
            ->with('estado')
            ->get();
        $this->porEstado = [
            'labels' => $dataEstado->map(fn($i) => $i->estado?->nombre ?? 'N/A')->toArray(),
            'data' => $dataEstado->pluck('total')->toArray(),
        ];

        // Por UN
        $dataUN = SolicitudDigitalizarLetra::select('unidad_negocio_id', DB::raw('count(*) as total'))
            ->groupBy('unidad_negocio_id')
            ->with('unidadNegocio')
            ->orderByDesc('total')
            ->take(8)
            ->get();
        $this->porUnidadNegocio = [
            'labels' => $dataUN->map(fn($i) => $i->unidadNegocio?->nombre ?? 'N/A')->toArray(),
            'data' => $dataUN->pluck('total')->toArray(),
        ];

        // Por Proyecto
        $dataProy = SolicitudDigitalizarLetra::select('proyecto_id', DB::raw('count(*) as total'))
            ->groupBy('proyecto_id')
            ->with('proyecto')
            ->orderByDesc('total')
            ->take(8)
            ->get();
        $this->porProyecto = [
            'labels' => $dataProy->map(fn($i) => $i->proyecto?->nombre ?? 'N/A')->toArray(),
            'data' => $dataProy->pluck('total')->toArray(),
        ];
    }

    private function cargarTendenciaRegistro()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $data = SolicitudDigitalizarLetra::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $this->tendenciaRegistro = [
            'labels' => array_values(range(1, $diasDelMes)),
            'data' => array_values(collect(range(1, $diasDelMes))->map(fn($d) => $data[$d] ?? 0)->toArray()),
        ];
    }

    private function cargarTablasRecientes()
    {
        $this->ultimasSolicitudes = SolicitudDigitalizarLetra::with(['unidadNegocio', 'proyecto', 'estado', 'userCliente'])
            ->latest()
            ->take(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.erp.reporte.letra.reporte-letra');
    }
}
