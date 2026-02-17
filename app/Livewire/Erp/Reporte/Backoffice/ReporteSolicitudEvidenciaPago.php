<?php

namespace App\Livewire\Erp\Reporte\Backoffice;

use App\Models\SolicitudEvidenciaPago;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Layout('layouts.erp.layout-erp')]
#[Title('Reporte Solicitud Evidencia Pago')]
class ReporteSolicitudEvidenciaPago extends Component
{
    public $totalSolicitudes;
    public $solicitudesSinAsignar;
    public $solicitudesAsignadas;
    public $solicitudesPorEstado = [];
    public $solicitudesValidadas = [];
    public $solicitudesPorUnidad = [];
    public $solicitudesPorProyecto = [];
    public $solicitudesPorFecha = [];
    public $topGestores = [];
    public $solicitudesPorCantidadEvidencias = [];

    public function mount()
    {
        $this->cargarTotales();
        $this->cargarPorEstado();
        $this->cargarValidadas();
        $this->cargarPorUnidad();
        $this->cargarPorProyecto();
        $this->cargarPorFecha();
        $this->cargarTopGestores();
        $this->cargarSolicitudesPorCantidadEvidencias();
    }

    private function cargarTotales()
    {
        $this->totalSolicitudes = SolicitudEvidenciaPago::count();
        $this->solicitudesSinAsignar = SolicitudEvidenciaPago::whereNull('gestor_id')->count();
        $this->solicitudesAsignadas = $this->totalSolicitudes - $this->solicitudesSinAsignar;
    }

    private function cargarPorEstado()
    {
        $data = SolicitudEvidenciaPago::select('estado_solicitud_evidencia_pago_id', DB::raw('count(*) as total'))
            ->groupBy('estado_solicitud_evidencia_pago_id')
            ->get();

        $this->solicitudesPorEstado = [
            'labels' => $data->map(fn($d) => $d->estado?->nombre ?? 'Desconocido'),
            'data' => $data->pluck('total'),
        ];
    }

    private function cargarValidadas()
    {
        $validadas = SolicitudEvidenciaPago::whereNotNull('fecha_validacion')->count();
        $pendientes = $this->totalSolicitudes - $validadas;

        $this->solicitudesValidadas = [
            'labels' => ['Validadas', 'Pendientes'],
            'data' => [$validadas, $pendientes],
        ];
    }

    private function cargarPorUnidad()
    {
        $data = SolicitudEvidenciaPago::select('unidad_negocio_id', DB::raw('count(*) as total'))
            ->groupBy('unidad_negocio_id')
            ->get();

        $this->solicitudesPorUnidad = [
            'labels' => $data->map(fn($d) => $d->unidadNegocio?->nombre ?? 'Desconocido'),
            'data' => $data->pluck('total'),
        ];
    }

    private function cargarPorProyecto()
    {
        $data = SolicitudEvidenciaPago::select('proyecto_id', DB::raw('count(*) as total'))
            ->groupBy('proyecto_id')
            ->get();

        $this->solicitudesPorProyecto = [
            'labels' => $data->map(fn($d) => $d->proyecto?->nombre ?? 'Desconocido'),
            'data' => $data->pluck('total'),
        ];
    }

    private function cargarPorFecha()
    {
        $data = SolicitudEvidenciaPago::select(DB::raw('DATE(created_at) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $this->solicitudesPorFecha = [
            'labels' => $data->pluck('fecha'),
            'data' => $data->pluck('total'),
        ];
    }

    private function cargarTopGestores()
    {
        $data = SolicitudEvidenciaPago::select('gestor_id', DB::raw('count(*) as total'))
            ->whereNotNull('fecha_validacion')
            ->groupBy('gestor_id')
            ->orderByDesc('total')
            ->with('gestor')
            ->take(5)
            ->get();

        $this->topGestores = [
            'labels' => $data->map(fn($d) => $d->gestor?->name ?? 'Desconocido'),
            'data' => $data->pluck('total'),
        ];
    }

    private function cargarSolicitudesPorCantidadEvidencias()
    {
        $solicitudes = SolicitudEvidenciaPago::withCount('evidencias')->get();

        $unaEvidencia = $solicitudes->where('evidencias_count', 1)->count();

        $masDeUna = $solicitudes->where('evidencias_count', '>', 1)->count();

        $this->solicitudesPorCantidadEvidencias = [
            'labels' => ['1 evidencia', 'Más de 1 evidencia'],
            'data' => [$unaEvidencia, $masDeUna],
        ];
    }

    public function render()
    {
        return view('livewire.erp.reporte.backoffice.reporte-solicitud-evidencia-pago');
    }
}
