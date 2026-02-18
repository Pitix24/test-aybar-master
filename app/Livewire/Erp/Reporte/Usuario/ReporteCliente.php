<?php

namespace App\Livewire\Erp\Reporte\Usuario;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte Cliente')]
class ReporteCliente extends Component
{
    // KPIs Principales
    public $totalClientes;
    public $clientesActivos;
    public $clientesInactivos;
    public $clientesEsteMes;
    public $crecimientoMensual;

    // KPIs Adicionales
    public $clientesConFoto;
    public $clientesPassCambiada;

    // Graficos
    public $clientesPorMes = [];
    public $clientesPorDiaMesActual = [];
    public $clientesPoliticas = [];
    public $clientesEmailVerificado = [];
    public $clientesConDireccion = [];
    public $clientesRadar = [];
    public $clientesPorRegion = [];

    // Nuevos Graficos
    public $clientesPorHora = [];
    public $clientesDominios = [];
    public $perfilCompletitud = [];

    // Tablas
    public $ultimosClientes = [];

    public function mount()
    {
        // Optimizamos la carga
        $clientesQuery = Cliente::with(['user', 'user.direccion.region']);
        $clientes = $clientesQuery->get();

        $this->totalClientes = $clientes->count();
        $this->clientesActivos = $clientes->filter(fn($c) => $c->user?->activo)->count();
        $this->clientesInactivos = $this->totalClientes - $this->clientesActivos;
        $this->clientesConFoto = $clientes->filter(fn($c) => !empty($c->user?->profile_photo_path))->count();
        $this->clientesPassCambiada = $clientes->filter(fn($c) => !empty($c->user?->password_changed_at))->count();

        $this->calcularCrecimientoMensual($clientes);
        $this->cargarClientesPorMes($clientes);
        $this->cargarClientesPorDiaMesActual($clientes);
        $this->cargarClientesPoliticas($clientes);
        $this->cargarClientesEmailVerificado($clientes);
        $this->cargarClientesConDireccion($clientes);
        $this->cargarRadarPerfil($clientes);
        $this->cargarClientesPorRegion($clientes);
        $this->cargarDistribucionHoraria($clientes);
        $this->cargarDominiosEmail($clientes);
        $this->cargarCompletitudDatos($clientes);

        // Últimos 10 registros
        $this->ultimosClientes = $clientes->sortByDesc('created_at')->take(10);
    }

    private function cargarDistribucionHoraria($clientes)
    {
        $data = $clientes->groupBy(fn($c) => $c->created_at->hour)
            ->map(fn($group) => $group->count());

        $labels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', range(0, 23));
        $values = array_map(fn($h) => $data[$h] ?? 0, range(0, 23));

        $this->clientesPorHora = [
            'labels' => array_values($labels),
            'data' => array_values($values),
        ];
    }

    private function cargarDominiosEmail($clientes)
    {
        $dominios = $clientes->map(function ($c) {
            $parts = explode('@', $c->email);
            return count($parts) > 1 ? strtolower($parts[1]) : 'otros';
        })->groupBy(fn($domain) => $domain)
            ->map(fn($group) => $group->count())
            ->sortByDesc(fn($count) => $count);

        $topDominios = $dominios->take(4);
        $otros = $dominios->slice(4)->sum();

        if ($otros > 0) {
            $topDominios['Otros'] = $otros;
        }

        $this->clientesDominios = [
            'labels' => $topDominios->keys()->toArray(),
            'data' => $topDominios->values()->toArray(),
        ];
    }

    private function cargarCompletitudDatos($clientes)
    {
        $total = $this->totalClientes ?: 1;

        $this->perfilCompletitud = [
            'labels' => ['Nombre', 'DNI', 'Email', 'Teléfono', 'Dirección', 'Foto'],
            'data' => [
                round(($clientes->filter(fn($c) => !empty($c->nombre))->count() / $total) * 100),
                round(($clientes->filter(fn($c) => !empty($c->dni))->count() / $total) * 100),
                round(($clientes->filter(fn($c) => !empty($c->email))->count() / $total) * 100),
                round(($clientes->filter(fn($c) => !empty($c->telefono_principal))->count() / $total) * 100),
                round(($clientes->filter(fn($c) => $c->user?->direccion)->count() / $total) * 100),
                round(($clientes->filter(fn($c) => !empty($c->user?->profile_photo_path))->count() / $total) * 100),
            ],
        ];
    }

    private function calcularCrecimientoMensual($clientes)
    {
        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;
        $mesPasado = Carbon::now()->subMonth()->month;
        $anioMesPasado = Carbon::now()->subMonth()->year;

        $this->clientesEsteMes = $clientes->filter(
            fn($c) =>
            $c->created_at->month == $mesActual && $c->created_at->year == $anioActual
        )->count();

        $clientesMesPasado = $clientes->filter(
            fn($c) =>
            $c->created_at->month == $mesPasado && $c->created_at->year == $anioMesPasado
        )->count();

        if ($clientesMesPasado > 0) {
            $this->crecimientoMensual = (($this->clientesEsteMes - $clientesMesPasado) / $clientesMesPasado) * 100;
        } else {
            $this->crecimientoMensual = $this->clientesEsteMes > 0 ? 100 : 0;
        }
    }

    private function cargarClientesPorMes($clientes)
    {
        $data = $clientes->groupBy(fn($c) => $c->created_at->format('Y-m'))
            ->map(fn($group) => $group->count());

        // Traer últimos 12 meses
        $labels = [];
        $values = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $labels[] = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $values[] = $data[$key] ?? 0;
        }

        $this->clientesPorMes = [
            'labels' => array_values($labels),
            'data' => array_values($values),
        ];
    }

    private function cargarClientesPorDiaMesActual($clientes)
    {
        $hoy = Carbon::now();
        $diasDelMes = $hoy->daysInMonth;

        $data = $clientes->filter(fn($c) => $c->created_at->month == $hoy->month && $c->created_at->year == $hoy->year)
            ->groupBy(fn($c) => $c->created_at->day)
            ->map(fn($group) => $group->count());

        $this->clientesPorDiaMesActual = [
            'labels' => array_values(range(1, $diasDelMes)),
            'data' => array_values(array_map(fn($d) => $data[$d] ?? 0, range(1, $diasDelMes))),
        ];
    }

    private function cargarClientesPoliticas($clientes)
    {
        $soloUno = $clientes->filter(fn($c) => $c->user?->politica_uno && !$c->user?->politica_dos)->count();
        $ambos = $clientes->filter(fn($c) => $c->user?->politica_uno && $c->user?->politica_dos)->count();
        $ninguno = $this->totalClientes - $soloUno - $ambos;

        $this->clientesPoliticas = [
            'labels' => ['Solo Política 1', 'Ambas Políticas', 'Ninguna'],
            'data' => [$soloUno, $ambos, $ninguno],
        ];
    }

    private function cargarClientesEmailVerificado($clientes)
    {
        $verificados = $clientes->filter(fn($c) => $c->user?->email_verified_at)->count();
        $noVerificados = $this->totalClientes - $verificados;

        $this->clientesEmailVerificado = [
            'labels' => ['Verificado', 'No verificado'],
            'data' => [$verificados, $noVerificados],
        ];
    }

    private function cargarClientesConDireccion($clientes)
    {
        $conDireccion = $clientes->filter(fn($c) => $c->user?->direccion)->count();
        $sinDireccion = $this->totalClientes - $conDireccion;

        $this->clientesConDireccion = [
            'labels' => ['Con dirección', 'Sin dirección'],
            'data' => [$conDireccion, $sinDireccion],
        ];
    }

    private function cargarRadarPerfil($clientes)
    {
        // Calculamos porcentajes para el radar
        $activo = $this->totalClientes > 0 ? ($this->clientesActivos / $this->totalClientes) * 100 : 0;
        $email = $this->totalClientes > 0 ? ($clientes->filter(fn($c) => $c->user?->email_verified_at)->count() / $this->totalClientes) * 100 : 0;
        $direccion = $this->totalClientes > 0 ? ($clientes->filter(fn($c) => $c->user?->direccion)->count() / $this->totalClientes) * 100 : 0;
        $politicas = $this->totalClientes > 0 ? ($clientes->filter(fn($c) => $c->user?->politica_uno && $c->user?->politica_dos)->count() / $this->totalClientes) * 100 : 0;
        $telefono = $this->totalClientes > 0 ? ($clientes->filter(fn($c) => !empty($c->telefono_principal))->count() / $this->totalClientes) * 100 : 0;

        $this->clientesRadar = [
            'labels' => ['Activos', 'Email Verif.', 'Dirección', 'Políticas', 'Teléfono'],
            'data' => [round($activo), round($email), round($direccion), round($politicas), round($telefono)],
        ];
    }

    private function cargarClientesPorRegion($clientes)
    {
        $data = $clientes->filter(fn($c) => $c->user?->direccion?->region)
            ->groupBy(fn($c) => $c->user->direccion->region->nombre)
            ->map(fn($group) => $group->count())
            ->sortByDesc(fn($count) => $count)
            ->take(5);

        $this->clientesPorRegion = [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.erp.reporte.usuario.reporte-cliente');
    }
}

