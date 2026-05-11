<?php

namespace App\Livewire\Erp\LibroReclamacion;

use App\Models\EstadoTicket;
use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tickets Libro Reclamacion')]
class LibroReclamacionLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(as: 'estado')]
    public $estado_filtro = '';

    #[Url]
    public $clasificacion = '';

    #[Url]
    public $gestor_id = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    #[Url]
    public $desde = '';

    #[Url]
    public $hasta = '';

    #[Url]
    public $perPage = 20;

    public $gestores = [];
    public $unidades = [];
    public $proyectos = [];
    public $estadosTicket = [];
    public $stats = [];

    public function mount(): void
    {
        $this->authorize('ticket-libro-reclamacion.lista');

        $this->gestores = User::query()
            ->where('activo', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->unidades = UnidadNegocio::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $this->proyectos = Proyecto::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $this->estadosTicket = EstadoTicket::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $this->cargarStats();
    }

    public function updated($property): void
    {
        if (
            in_array($property, [
                'buscar',
                'estado_filtro',
                'clasificacion',
                'gestor_id',
                'unidad_negocio_id',
                'proyecto_id',
                'desde',
                'hasta',
                'perPage',
            ], true)
        ) {
            $this->resetPage();
            $this->cargarStats();
        }
    }

    public function filterTotal(): void
    {
        $this->estado_filtro = '';
        $this->clasificacion = '';
        $this->resetPage();
        $this->cargarStats();
    }

    public function filterResueltos(): void
    {
        $this->estado_filtro = EstadoTicket::id(EstadoTicket::CERRADO);
        $this->resetPage();
        $this->cargarStats();
    }

    public function filterPendientes(): void
    {
        $this->estado_filtro = 'PENDIENTES';
        $this->resetPage();
        $this->cargarStats();
    }

    public function filterNoProcede(): void
    {
        $this->estado_filtro = 'NO_PROCEDE';
        $this->resetPage();
        $this->cargarStats();
    }

    public function cargarStats(array $filters = []): void
    {
        $baseQuery = LibroReclamacion::query()
            ->when($this->proyecto_id !== '', fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->gestor_id !== '', fn($q) => $q->where('gestor_id', $this->gestor_id))
            ->when($this->unidad_negocio_id !== '', fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('codigo_ticket', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_documento', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_nombre', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_email', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_celular', 'like', "%{$this->buscar}%");
                });
            });

        $total = (clone $baseQuery)->count();

        // Resueltos: reclamos cuyo ticket relacionado está CERRADO o ATENDIDO
        $estadosCerrado = array_filter([
            EstadoTicket::id(EstadoTicket::CERRADO),
            EstadoTicket::id(EstadoTicket::ATENDIDO),
        ]);

        $resueltos = !empty($estadosCerrado)
            ? (clone $baseQuery)
            ->whereHas('ticketRelacionado', fn($q) => $q->whereIn('estado_ticket_id', $estadosCerrado))
            ->count()
            : 0;

        // Pendientes: tienen ticket relacionado en estados de gestión activa
        $pendientesEstados = array_filter([
            EstadoTicket::id(EstadoTicket::NUEVO),
            EstadoTicket::id(EstadoTicket::EN_GESTION),
            EstadoTicket::id(EstadoTicket::EN_ESPERA_CLIENTE),
            EstadoTicket::id(EstadoTicket::DERIVADO),
        ]);

        $pendientes = (clone $baseQuery)
            ->where('clasificacion', '!=', 'NO_PROCEDE')
            ->where(function ($q) use ($pendientesEstados) {
                // Sin ticket vinculado (reclamo NUEVO sin ticket auto-creado)
                $q->whereNull('ticket_id');
                // O con ticket en estados activos
                if (!empty($pendientesEstados)) {
                    $q->orWhereHas('ticketRelacionado', fn($t) => $t->whereIn('estado_ticket_id', $pendientesEstados));
                }
            })
            ->count();

        // No procedentes: clasificación NO_PROCEDE
        $no_procede = (clone $baseQuery)
            ->where('clasificacion', 'NO_PROCEDE')
            ->count();

        // El promedio por día debe medir la antigüedad real del módulo,
        // por eso toma el primer libro registrado en toda la tabla.
        $minFecha = LibroReclamacion::query()->min('created_at');
        $dias = 1;
        $fechaBasePromedio = null;
        if ($minFecha) {
            $fechaBasePromedio = Carbon::parse($minFecha);
            // +1 para incluir tanto el día inicial como el día actual en el rango
            $dias = $fechaBasePromedio->copy()->startOfDay()->diffInDays(Carbon::now()->startOfDay(), true) + 1;
        }
        $dias = max(1, $dias);
        $promedio = (int) round($total / $dias);

        $this->stats = [
            'total' => $total,
            'resueltos' => $resueltos,
            'pendientes' => $pendientes,
            'no_procede' => $no_procede,
            'promedio_por_dia' => $promedio,
            'dias' => $dias,
            'fecha_base_promedio' => $fechaBasePromedio?->format('d/m/Y'),
        ];
    }

    public function resetFiltros(): void
    {
        $this->reset([
            'buscar',
            'estado_filtro',
            'clasificacion',
            'gestor_id',
            'unidad_negocio_id',
            'proyecto_id',
            'desde',
            'hasta',
        ]);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $items = LibroReclamacion::query()
            ->with(['ticketRelacionado.estado', 'proyecto', 'unidadNegocio', 'gestor', 'cliente'])
            ->when($this->buscar !== '', function ($q): void {
                $q->where(function ($sub): void {
                    $sub->where('codigo_ticket', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_documento', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_nombre', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_email', 'like', "%{$this->buscar}%")
                        ->orWhere('cliente_celular', 'like', "%{$this->buscar}%")
                        ->orWhereHas('cliente', function ($cliente): void {
                            $cliente->where('name', 'like', "%{$this->buscar}%")
                                ->orWhere('email', 'like', "%{$this->buscar}%")
                                ->orWhereHas('perfilCliente', function ($perfil): void {
                                    $perfil->where('dni', 'like', "%{$this->buscar}%")
                                        ->orWhere('telefono_principal', 'like', "%{$this->buscar}%");
                                });
                        });
                });
            })
            ->when($this->estado_filtro !== '', function ($q): void {
                if ($this->estado_filtro === 'NO_PROCEDE') {
                    $q->where('clasificacion', 'NO_PROCEDE');

                    return;
                }

                if ($this->estado_filtro === 'PENDIENTES') {
                    $pendientesEstados = array_filter([
                        EstadoTicket::id(EstadoTicket::EN_GESTION),
                        EstadoTicket::id(EstadoTicket::EN_ESPERA_CLIENTE),
                        EstadoTicket::id(EstadoTicket::DERIVADO),
                    ]);

                    $q->whereHas('ticketRelacionado', fn($ticket) => $ticket->whereIn('estado_ticket_id', $pendientesEstados));

                    return;
                }

                $estadoId = (int) $this->estado_filtro;
                if ($estadoId > 0) {
                    $q->whereHas('ticketRelacionado', fn($ticket) => $ticket->where('estado_ticket_id', $estadoId));
                }
            })
            ->when($this->clasificacion !== '', fn($q) => $q->where('clasificacion', $this->clasificacion))
            ->when($this->gestor_id !== '', fn($q) => $q->where('gestor_id', $this->gestor_id))
            ->when($this->unidad_negocio_id !== '', fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id !== '', fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderByDesc('ticket')
            ->paginate($this->perPage);

        return view('livewire.erp.libro-reclamacion.libro-reclamacion-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
