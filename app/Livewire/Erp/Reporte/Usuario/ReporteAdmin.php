<?php

namespace App\Livewire\Erp\Reporte\Usuario;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Administradores')]
class ReporteAdmin extends Component
{
    // KPIs
    public $totalAdmins;
    public $adminsActivos;
    public $adminsInactivos;
    public $nuevosEsteMes;

    // Gráficos
    public $distribucionRoles = [];
    public $tendenciaRegistro = [];
    public $actividadReciente = [];

    // Tablas
    public $ultimosAdmins = [];

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
        $query = User::where('rol', 'admin');

        $this->totalAdmins = (clone $query)->count();
        $this->adminsActivos = (clone $query)->whereNull('deleted_at')->count();
        $this->adminsInactivos = (clone $query)->onlyTrashed()->count();

        $this->nuevosEsteMes = (clone $query)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $this->cargarDistribucionRoles();
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

    private function cargarDistribucionRoles()
    {
        // Usando Spatie roles para admins
        $roles = DB::table('roles')
            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->where('users.rol', 'admin')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->groupBy('roles.name')
            ->get();

        $this->distribucionRoles = [
            'labels' => $roles->pluck('name')->toArray(),
            'data' => $roles->pluck('total')->toArray(),
        ];
    }

    private function cargarTendenciaRegistro()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $data = User::where('rol', 'admin')
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
        $this->ultimosAdmins = User::where('rol', 'admin')
            ->with('roles')
            ->latest()
            ->take(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.erp.reporte.usuario.reporte-admin');
    }
}
