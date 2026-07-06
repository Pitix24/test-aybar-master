<?php

namespace App\Livewire\Erp\Reporte\Atc;

use App\Models\Ticket;
use App\Models\EstadoTicket;
use App\Models\TicketDerivado;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Tickets')]
class ReporteTicket extends Component
{
    // KPIs
    public $totalTickets;
    public $ticketsCerrados;
    public $ticketsAbiertos;
    public $ticketsVencidos;

    // Gráficos
    public $ticketsPorEstado = [];
    public $ticketsPorArea = [];
    public $ticketsPorDiaMes = [];
    public $ticketsPorCanal = [];
    public $ticketsPorTipo = [];
    public $rankingGestores = [];
    public $distribucionPrioridad = [];

    // Tablas
    public $ultimosTickets = [];
    public $ultimasDerivaciones = [];

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
        $this->totalTickets = Ticket::count();
        $this->ticketsCerrados = Ticket::whereHas('estado', function ($q) {
            $q->where('nombre', EstadoTicket::CERRADO);
        })->count();
        $this->ticketsAbiertos = $this->totalTickets - $this->ticketsCerrados;

        // Simulación o cálculo de vencidos si existe el campo, por ahora asumimos lógica base
        $this->ticketsVencidos = Ticket::whereNull('fecha_validacion')
            ->where('created_at', '<', Carbon::now()->subDays(3))
            ->count();

        $this->cargarDistribucionEstatica();
    }

    public function updatedMesSeleccionado()
    {
        $this->actualizarGraficosMensuales();

        $this->dispatch('actualizarGraficosDinamicos', [
            'tendencia' => $this->ticketsPorDiaMes,
            'gestores' => $this->rankingGestores
        ]);
    }

    private function actualizarGraficosMensuales()
    {
        $this->cargarTicketsPorDiaMes();
        $this->cargarRankingGestores();
    }

    private function cargarDistribucionEstatica()
    {
        // Estados
        $dataEstado = Ticket::select('estado_ticket_id', DB::raw('count(*) as total'))
            ->groupBy('estado_ticket_id')
            ->with('estado')
            ->get();
        $this->ticketsPorEstado = [
            'labels' => $dataEstado->map(fn($i) => $i->estado?->nombre ?? 'Desconocido')->toArray(),
            'data' => $dataEstado->pluck('total')->toArray(),
        ];

        // Áreas
        $dataArea = Ticket::select('area_id', DB::raw('count(*) as total'))
            ->groupBy('area_id')
            ->with('area')
            ->orderByDesc('total')
            ->take(8)
            ->get();
        $this->ticketsPorArea = [
            'labels' => $dataArea->map(fn($i) => $i->area?->nombre ?? 'Sin área')->toArray(),
            'data' => $dataArea->pluck('total')->toArray(),
        ];

        // Canales
        $dataCanal = Ticket::select('canal_id', DB::raw('count(*) as total'))
            ->groupBy('canal_id')
            ->with('canal')
            ->get();
        $this->ticketsPorCanal = [
            'labels' => $dataCanal->map(fn($i) => $i->canal?->nombre ?? 'Otros')->toArray(),
            'data' => $dataCanal->pluck('total')->toArray(),
        ];

        // Tipos de Solicitud
        $dataTipo = Ticket::select('tipo_solicitud_id', DB::raw('count(*) as total'))
            ->groupBy('tipo_solicitud_id')
            ->with('tipoSolicitud')
            ->orderByDesc('total')
            ->take(8)
            ->get();
        $this->ticketsPorTipo = [
            'labels' => $dataTipo->map(fn($i) => $i->tipoSolicitud?->nombre ?? 'Otros')->toArray(),
            'data' => $dataTipo->pluck('total')->toArray(),
        ];
    }

    private function cargarTicketsPorDiaMes()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);
        $fecha = Carbon::createFromDate($year, $month, 1);
        $diasDelMes = $fecha->daysInMonth;

        $creados = Ticket::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DAY(created_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $cerrados = Ticket::whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->whereHas('estado', fn($q) => $q->where('nombre', EstadoTicket::CERRADO))
            ->selectRaw('DAY(updated_at) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia');

        $this->ticketsPorDiaMes = [
            'labels' => array_values(range(1, $diasDelMes)),
            'creados' => array_values(collect(range(1, $diasDelMes))->map(fn($d) => $creados[$d] ?? 0)->toArray()),
            'cerrados' => array_values(collect(range(1, $diasDelMes))->map(fn($d) => $cerrados[$d] ?? 0)->toArray()),
        ];
    }

    private function cargarRankingGestores()
    {
        if (empty($this->mesSeleccionado))
            return;
        [$year, $month] = explode('-', $this->mesSeleccionado);

        $data = Ticket::select('gestor_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('gestor_id')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
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
        $this->ultimosTickets = Ticket::with(['userCliente', 'area', 'estado', 'prioridad'])
            ->latest()
            ->take(5)
            ->get();

        $this->ultimasDerivaciones = TicketDerivado::with(['ticket', 'deArea', 'aArea', 'usuarioDeriva', 'usuarioRecibe'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.erp.reporte.atc.reporte-ticket');
    }
}
