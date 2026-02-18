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
    public $totalClientes;
    //public $clientesActivos;
    public $clientesPorMes = [];
    public $clientesPorDiaMesActual = [];
    public $clientesPoliticas = [];
    public $usuariosEmailVerificado = [];
    public $clientesEmailVerificado = [];
    public $clientesConDireccion = [];
    public $clientesRadar = [];

    public function mount()
    {
        $clientes = Cliente::with(['user', 'user.direccion'])->get();

        $this->totalClientes = $clientes->count();
        //$this->clientesActivos = $clientes->filter(fn($c) => $c->user?->activo)->count();

        $this->cargarClientesPorMes($clientes);
        $this->cargarClientesPorDiaMesActual($clientes);
        $this->cargarClientesPoliticas($clientes);
        $this->cargarClientesEmailVerificado($clientes);
        $this->cargarClientesConDireccion($clientes);
        $this->cargarRadarPerfil($clientes);
    }

    private function cargarClientesPorMes($clientes)
    {
        $data = $clientes->groupBy(fn($c) => $c->created_at->month)
            ->map(fn($group) => $group->count());

        $this->clientesPorMes = [
            'labels' => collect(range(1, 12))->map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)))->toArray(),
            'data' => array_map(fn($m) => $data[$m] ?? 0, range(1, 12)),
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
            'labels' => range(1, $diasDelMes),
            'data' => array_map(fn($d) => $data[$d] ?? 0, range(1, $diasDelMes)),
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
        $activo = $clientes->filter(fn($c) => $c->user?->activo)->count();

        $email = $clientes->filter(fn($c) => $c->user?->email_verified_at)->count();

        $direccion = $clientes->filter(fn($c) => $c->user?->direccion)->count();

        $ambasPoliticas = $clientes->filter(fn($c) => $c->user?->politica_uno && $c->user?->politica_dos)->count();

        $this->clientesRadar = [
            'labels' => ['Activos', 'Email verificado', 'Con dirección', 'Aceptaron ambas políticas'],
            'data' => [$activo, $email, $direccion, $ambasPoliticas],
        ];
    }

    public function render()
    {
        return view('livewire.erp.reporte.usuario.reporte-cliente');
    }
}
