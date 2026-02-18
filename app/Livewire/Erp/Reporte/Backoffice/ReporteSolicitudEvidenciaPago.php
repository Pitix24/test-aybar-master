<?php

namespace App\Livewire\Erp\Reporte\Backoffice;

use App\Models\SolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaPagoEmail;
use App\Models\EvidenciaPago;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte Evidencias de Pago')]
class ReporteSolicitudEvidenciaPago extends Component
{
    // KPIs Principales
    public $totalSolicitudes;
    public $solicitudesSinAsignar;
    public $solicitudesAsignadas;
    public $solicitudesValidadas;
    public $solicitudesPendientes;
    public $tasaCumplimiento;
    public $totalEmailsEnviados;
    public $totalArchivosEvidencia;
    public $evidenciasAntiguasCount = 0;

    // Graficos Solicitudes
    public $solicitudesPorEstado = [];
    public $solicitudesPorUnidad = [];
    public $solicitudesPorProyecto = [];
    public $solicitudesPorDiaMesActual = [];
    public $topGestores = [];
    public $solicitudesPorCantidadEvidencias = [];

    // Graficos Evidencias (Archivos)
    public $evidenciasPorEstado = [];
    public $evidenciasSubidasPorDia = [];
    public $distribucionBancos = [];
    public $emailsPorGestor = [];
    public $clientesPorHora = [];
    public $antiguedadEvidencias = []; // Nueva métrica

    // Tablas "Últimos 5"
    public $ultimasSolicitudes = [];
    public $ultimasEvidencias = [];
    public $ultimosEmails = [];

    // Filtros Dinámicos
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
        $this->totalSolicitudes = SolicitudEvidenciaPago::count();
        $this->solicitudesSinAsignar = SolicitudEvidenciaPago::whereNull('gestor_id')->count();
        $this->solicitudesAsignadas = $this->totalSolicitudes - $this->solicitudesSinAsignar;

        $validadasCount = SolicitudEvidenciaPago::whereNotNull('fecha_validacion')->count();
        $this->solicitudesValidadas = $validadasCount;
        $this->solicitudesPendientes = $this->totalSolicitudes - $validadasCount;

        $this->tasaCumplimiento = $this->totalSolicitudes > 0 ? round(($validadasCount / $this->totalSolicitudes) * 100, 1) : 0;
        $this->totalEmailsEnviados = SolicitudEvidenciaPagoEmail::count();
        $this->totalArchivosEvidencia = EvidenciaPago::count();

        $this->cargarPorEstado();
        $this->cargarPorUnidad();
        $this->cargarPorProyecto();
        $this->cargarTopGestores();
        $this->cargarSolicitudesPorCantidadEvidencias();
        $this->cargarDistribucionBancos();
        $this->cargarEvidenciasPorEstado();
        $this->cargarAntiguedadEvidencias();
    }

    public function updatedMesSeleccionado()
    {
        $this->actualizarGraficosMensuales();

        $this->dispatch('actualizarGraficosDinamicos', [
            'tendencia' => $this->solicitudesPorDiaMesActual,
            'emails' => $this->emailsPorGestor,
            'horarios' => $this->clientesPorHora,
            'subidas' => $this->evidenciasSubidasPorDia
        ]);
    }

    private function actualizarGraficosMensuales()
    {
        $this->cargarSolicitudesPorDiaMesActual();
        $this->cargarEvidenciasSubidasPorDia();
        $this->cargarMetricasComunicacion();
        $this->cargarDistribucionHoraria();
    }

    private function cargarAntiguedadEvidencias()
    {
        // Agrupamos por el año de la fecha extraída del documento (OpenAI)
        $data = EvidenciaPago::select(DB::raw('YEAR(fecha) as anio'), DB::raw('count(*) as total'))
            ->whereNotNull('fecha')
            ->groupBy('anio')
            ->orderByDesc('anio')
            ->get();

        $this->antiguedadEvidencias = [
            'labels' => array_values($data->pluck('anio')->map(fn($y) => "Año " . $y)->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];

        // Conteo de evidencias que no son del año actual
        $anioActual = Carbon::now()->year;
        $this->evidenciasAntiguasCount = EvidenciaPago::whereNotNull('fecha')
            ->whereYear('fecha', '<', $anioActual)
            ->count();
    }

    private function cargarTablasRecientes()
    {
        $this->ultimasSolicitudes = SolicitudEvidenciaPago::with(['unidadNegocio', 'proyecto', 'estado', 'gestor'])
            ->latest()
            ->take(5)
            ->get();

        $this->ultimasEvidencias = EvidenciaPago::with(['solicitud', 'solicitud.proyecto'])
            ->latest()
            ->take(5)
            ->get();

        $this->ultimosEmails = SolicitudEvidenciaPagoEmail::with(['emisor', 'receptor'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function cargarSolicitudesPorDiaMesActual()
    {
        if (empty($this->mesSeleccionado) || !str_contains($this->mesSeleccionado, '-')) {
            $this->solicitudesPorDiaMesActual = ['labels' => [], 'data' => []];
            return;
        }

        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $data = SolicitudEvidenciaPago::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $this->solicitudesPorDiaMesActual = [
            'labels' => array_values(range(1, $diasDelMes)),
            'data' => array_values(collect(range(1, $diasDelMes))
                ->map(fn($dia) => $data[$dia] ?? 0)
                ->toArray()),
        ];
    }

    private function cargarEvidenciasSubidasPorDia()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);

        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $data = EvidenciaPago::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $this->evidenciasSubidasPorDia = [
            'labels' => array_values(range(1, $diasDelMes)),
            'data' => array_values(collect(range(1, $diasDelMes))
                ->map(fn($dia) => $data[$dia] ?? 0)
                ->toArray()),
        ];
    }

    private function cargarEvidenciasPorEstado()
    {
        $data = EvidenciaPago::select('estado_solicitud_evidencia_pago_id', DB::raw('count(*) as total'))
            ->groupBy('estado_solicitud_evidencia_pago_id')
            ->get();

        $nombres = [
            1 => 'Pendiente',
            2 => 'Observado',
            3 => 'Aprobado',
            4 => 'Rechazado'
        ];

        $this->evidenciasPorEstado = [
            'labels' => array_values($data->map(fn($d) => $nombres[$d->estado_solicitud_evidencia_pago_id] ?? 'Otro')->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarDistribucionHoraria()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);

        $data = SolicitudEvidenciaPago::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('HOUR(created_at) as hora, COUNT(*) as total')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        $labels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', range(0, 23));
        $values = array_map(fn($h) => $data[$h] ?? 0, range(0, 23));

        $this->clientesPorHora = [
            'labels' => $labels,
            'data' => $values,
        ];
    }

    private function cargarDistribucionBancos()
    {
        $data = EvidenciaPago::select('banco', DB::raw('count(*) as total'))
            ->whereNotNull('banco')
            ->groupBy('banco')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $this->distribucionBancos = [
            'labels' => array_values($data->pluck('banco')->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarMetricasComunicacion()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);

        $data = SolicitudEvidenciaPagoEmail::query()
            ->whereYear('enviado_at', $year)
            ->whereMonth('enviado_at', $month)
            ->select('emisor_id', DB::raw('count(*) as total'))
            ->groupBy('emisor_id')
            ->with('emisor')
            ->orderByDesc('total')
            ->get();

        $this->emailsPorGestor = [
            'labels' => array_values($data->map(fn($d) => explode(' ', $d->emisor?->name ?? 'Sistema')[0])->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarPorEstado()
    {
        $data = SolicitudEvidenciaPago::select('estado_solicitud_evidencia_pago_id', DB::raw('count(*) as total'))
            ->groupBy('estado_solicitud_evidencia_pago_id')
            ->with('estado')
            ->get();

        $this->solicitudesPorEstado = [
            'labels' => array_values($data->map(fn($d) => $d->estado?->nombre ?? 'Sin Estado')->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarPorUnidad()
    {
        $data = SolicitudEvidenciaPago::select('unidad_negocio_id', DB::raw('count(*) as total'))
            ->groupBy('unidad_negocio_id')
            ->with('unidadNegocio')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $this->solicitudesPorUnidad = [
            'labels' => array_values($data->map(fn($d) => $d->unidadNegocio?->nombre ?? 'Sin UN')->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarPorProyecto()
    {
        $data = SolicitudEvidenciaPago::select('proyecto_id', DB::raw('count(*) as total'))
            ->groupBy('proyecto_id')
            ->with('proyecto')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        $this->solicitudesPorProyecto = [
            'labels' => array_values($data->map(fn($d) => $d->proyecto?->nombre ?? 'Sin Proyecto')->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarTopGestores()
    {
        $data = SolicitudEvidenciaPago::select('gestor_id', DB::raw('count(*) as total'))
            ->whereNotNull('fecha_validacion')
            ->groupBy('gestor_id')
            ->orderByDesc('total')
            ->with('gestor')
            ->take(6)
            ->get();

        $this->topGestores = [
            'labels' => array_values($data->map(function ($d) {
                if (!$d->gestor)
                    return 'Sin Asignar';
                $nombres = explode(' ', $d->gestor->name);
                return $nombres[0];
            })->toArray()),
            'data' => array_values($data->pluck('total')->toArray()),
        ];
    }

    private function cargarSolicitudesPorCantidadEvidencias()
    {
        $solicitudes = SolicitudEvidenciaPago::withCount('evidencias')->get();
        $unaEvid = $solicitudes->where('evidencias_count', 1)->count();
        $multiEvid = $solicitudes->where('evidencias_count', '>', 1)->count();
        $sinEvid = $solicitudes->where('evidencias_count', 0)->count();

        $this->solicitudesPorCantidadEvidencias = [
            'labels' => ['Sin Evid.', '1 Evidencia', 'Múltiples'],
            'data' => [$sinEvid, $unaEvid, $multiEvid],
        ];
    }

    public function render()
    {
        return view('livewire.erp.reporte.backoffice.reporte-solicitud-evidencia-pago');
    }
}
