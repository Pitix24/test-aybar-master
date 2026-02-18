<?php

namespace App\Livewire\Erp\Reporte\Cita;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\Sede;
use App\Models\MotivoCita;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Citas')]
class ReporteCita extends Component
{
    // KPIs
    public $totalCitas;
    public $citasAtendidas;
    public $citasPendientes;
    public $citasCanceladas;
    public $tasaCumplimiento;

    // Gráficos
    public $porEstado = [];
    public $porSede = [];
    public $porMotivo = [];
    public $tendenciaCitas = [];
    public $rankingGestores = [];
    public $distribucionHoraria = [];

    // Tablas
    public $ultimasCitas = [];

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
        $this->totalCitas = Cita::count();
        $this->citasAtendidas = Cita::whereHas('estado', function ($q) {
            $q->where('nombre', 'ATENDIDO');
        })->count();
        $this->citasCanceladas = Cita::whereHas('estado', function ($q) {
            $q->where('nombre', 'CANCELADO');
        })->count();
        $this->citasPendientes = $this->totalCitas - $this->citasAtendidas - $this->citasCanceladas;

        $totalValidas = $this->totalCitas - $this->citasCanceladas;
        $this->tasaCumplimiento = $totalValidas > 0 ? round(($this->citasAtendidas / $totalValidas) * 100, 1) : 0;

        $this->cargarDistribucionEstatica();
    }

    public function updatedMesSeleccionado()
    {
        $this->actualizarGraficosMensuales();

        $this->dispatch('actualizarGraficosDinamicos', [
            'tendencia' => $this->tendenciaCitas,
            'gestores' => $this->rankingGestores
        ]);
    }

    private function actualizarGraficosMensuales()
    {
        $this->cargarTendenciaCitas();
        $this->cargarRankingGestores();
    }

    private function cargarDistribucionEstatica()
    {
        // Por Estado
        $dataEstado = Cita::select('estado_cita_id', DB::raw('count(*) as total'))
            ->groupBy('estado_cita_id')
            ->with('estado')
            ->get();
        $this->porEstado = [
            'labels' => $dataEstado->map(fn($i) => $i->estado?->nombre ?? 'N/A')->toArray(),
            'data' => $dataEstado->pluck('total')->toArray(),
        ];

        // Por Sede
        $dataSede = Cita::select('sede_id', DB::raw('count(*) as total'))
            ->groupBy('sede_id')
            ->with('sede')
            ->orderByDesc('total')
            ->get();
        $this->porSede = [
            'labels' => $dataSede->map(fn($i) => $i->sede?->nombre ?? 'Sin sede')->toArray(),
            'data' => $dataSede->pluck('total')->toArray(),
        ];

        // Por Motivo
        $dataMotivo = Cita::select('motivo_cita_id', DB::raw('count(*) as total'))
            ->groupBy('motivo_cita_id')
            ->with('motivo')
            ->orderByDesc('total')
            ->take(8)
            ->get();
        $this->porMotivo = [
            'labels' => $dataMotivo->map(fn($i) => $i->motivo?->nombre ?? 'Otros')->toArray(),
            'data' => $dataMotivo->pluck('total')->toArray(),
        ];
    }

    private function cargarTendenciaCitas()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $programadas = Cita::whereYear('fecha_inicio', $year)
            ->whereMonth('fecha_inicio', $month)
            ->selectRaw('DAY(fecha_inicio) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $this->tendenciaCitas = [
            'labels' => array_values(range(1, $diasDelMes)),
            'data' => array_values(collect(range(1, $diasDelMes))->map(fn($d) => $programadas[$d] ?? 0)->toArray()),
        ];
    }

    private function cargarRankingGestores()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);

        $data = Cita::select('gestor_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('gestor_id')
            ->whereYear('fecha_inicio', $year)
            ->whereMonth('fecha_inicio', $month)
            ->groupBy('gestor_id')
            ->orderByDesc('total')
            ->with('gestor')
            ->take(8)
            ->get();

        $this->rankingGestores = [
            'labels' => $data->map(fn($i) => explode(' ', $i->gestor?->name ?? 'Usuario')[0])->toArray(),
            'data' => $data->pluck('total')->toArray(),
        ];
    }

    private function cargarTablasRecientes()
    {
        $this->ultimasCitas = Cita::with(['cliente', 'sede', 'estado', 'motivo', 'gestor'])
            ->latest('fecha_inicio')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.erp.reporte.cita.reporte-cita');
    }
}
