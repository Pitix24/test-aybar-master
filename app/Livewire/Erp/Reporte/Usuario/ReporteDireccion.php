<?php

namespace App\Livewire\Erp\Reporte\Usuario;

use App\Models\Direccion;
use App\Models\Region;
use App\Models\Provincia;
use App\Models\Distrito;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Direcciones de Clientes')]
class ReporteDireccion extends Component
{
    // KPIs
    public $totalDirecciones;
    public $regionesCubiertas;
    public $distritosCubiertos;
    public $nuevasDireccionesMes;

    // Gráficos
    public $porRegion = [];
    public $porProvincia = [];
    public $porDistrito = [];
    public $tendenciaRegistro = [];

    // Tablas
    public $ultimasDirecciones = [];

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
        $query = Direccion::whereHas('user', function ($q) {
            $q->where('rol', 'cliente');
        });

        $this->totalDirecciones = (clone $query)->count();
        $this->regionesCubiertas = (clone $query)->distinct('region_id')->count('region_id');
        $this->distritosCubiertos = (clone $query)->distinct('distrito_id')->count('distrito_id');

        $this->nuevasDireccionesMes = (clone $query)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $this->cargarDistribucionGeografica();
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

    private function cargarDistribucionGeografica()
    {
        $baseQuery = Direccion::whereHas('user', function ($q) {
            $q->where('rol', 'cliente');
        });

        // Por Región
        $regiones = (clone $baseQuery)
            ->join('regions', 'direccions.region_id', '=', 'regions.id')
            ->select('regions.nombre', DB::raw('count(*) as total'))
            ->groupBy('regions.nombre')
            ->orderByDesc('total')
            ->get();

        $this->porRegion = [
            'labels' => $regiones->pluck('nombre')->toArray(),
            'data' => $regiones->pluck('total')->toArray(),
        ];

        // Por Provincia (Top 10)
        $provincias = (clone $baseQuery)
            ->join('provincias', 'direccions.provincia_id', '=', 'provincias.id')
            ->select('provincias.nombre', DB::raw('count(*) as total'))
            ->groupBy('provincias.nombre')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $this->porProvincia = [
            'labels' => $provincias->pluck('nombre')->toArray(),
            'data' => $provincias->pluck('total')->toArray(),
        ];

        // Por Distrito (Top 10)
        $distritos = (clone $baseQuery)
            ->join('distritos', 'direccions.distrito_id', '=', 'distritos.id')
            ->select('distritos.nombre', DB::raw('count(*) as total'))
            ->groupBy('distritos.nombre')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $this->porDistrito = [
            'labels' => $distritos->pluck('nombre')->toArray(),
            'data' => $distritos->pluck('total')->toArray(),
        ];
    }

    private function cargarTendenciaRegistro()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $data = Direccion::whereHas('user', function ($q) {
            $q->where('rol', 'cliente');
        })
            ->whereYear('created_at', $year)
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
        $this->ultimasDirecciones = Direccion::whereHas('user', function ($q) {
            $q->where('rol', 'cliente');
        })
            ->with(['user', 'region', 'provincia', 'distrito'])
            ->latest()
            ->take(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.erp.reporte.usuario.reporte-direccion');
    }
}
