<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Area;
use App\Models\Canal;
use App\Models\Proyecto;
use App\Models\SubTipoSolicitud;
use App\Models\Ticket;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use App\Models\TipoSolicitud;
use App\Models\UnidadNegocio;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Atc\TicketExport;
use Illuminate\Support\Facades\Auth;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tickets')]
class TicketLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $estado_id = '';

    #[Url(keep: true)]
    public $prioridad_id = '';

    #[Url(keep: true)]
    public $usuario_admin_id = null;

    #[Url(keep: true)]
    public $desde = null;

    #[Url(keep: true)]
    public $hasta = null;

    #[Url(keep: true)]
    public $unidad_negocio_id = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $area_id = '';

    #[Url(keep: true)]
    public $solicitud_id = '';

    #[Url(keep: true)]
    public $sub_tipo_solicitud_id = '';

    #[Url(keep: true)]
    public $canal_id = '';

    #[Url(keep: true)]
    public $con_derivados = '';

    #[Url(keep: true)]
    public $con_citas = '';

    #[Url(keep: true)]
    public $con_hijos = '';

    #[Url(keep: true)]
    public $perPage = 20;

    #[Url(keep: true)]
    public $creado_por_id = '';

    public $estados = [];
    public $areas = [];
    public $solicitudes = [];
    public $sub_tipo_solicitudes = [];
    public $canales = [];
    public $usuarios_admin = [];
    public $prioridades = [];
    public $unidades_negocios = [];
    public $proyectos = [];

    public function mount()
    {
        $this->estados = EstadoTicket::all();
        $this->areas = Area::all();
        $this->solicitudes = TipoSolicitud::all();
        $this->canales = Canal::all();
        $this->usuarios_admin = User::permission('atc.gestor')->get();
        $this->prioridades = PrioridadTicket::all();
        // Aplicamos filtros por defecto solo si NO están presentes en la URL
        // Esto permite que si regresas con ?desde=&hasta= (vacíos), se respeten.
        if (!request()->has('usuario_admin_id') && is_null($this->usuario_admin_id)) {
            $this->usuario_admin_id = Auth::check() ? Auth::id() : '';
        }
        if (!request()->has('desde') && is_null($this->desde)) {
            $this->desde = now()->toDateString();
        }
        if (!request()->has('hasta') && is_null($this->hasta)) {
            $this->hasta = now()->toDateString();
        }

        $this->unidades_negocios = UnidadNegocio::all();

        // Cargamos catálogos dependientes si hay un valor (venga de URL o de los defaults anteriores)
        if ($this->unidad_negocio_id) {
            $this->loadProyectos();
        }

        if ($this->solicitud_id) {
            $this->loadSubTipoSolicitudes();
        }
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        $this->proyectos = [];

        if ($value) {
            $this->loadProyectos();
        }
    }

    public function loadProyectos()
    {
        if (!is_null($this->unidad_negocio_id)) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->get();
        }
    }

    public function updatedSolicitudId($value)
    {
        $this->sub_tipo_solicitud_id = '';
        $this->sub_tipo_solicitudes = [];

        if ($value) {
            $this->loadSubTipoSolicitudes();
        }
    }

    public function loadSubTipoSolicitudes()
    {
        if (!is_null($this->solicitud_id)) {
            $this->sub_tipo_solicitudes = SubTipoSolicitud::where('tipo_solicitud_id', $this->solicitud_id)->get();
        }
    }


    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'unidad_negocio_id',
                'proyecto_id',
                'estado_id',
                'area_id',
                'solicitud_id',
                'sub_tipo_solicitud_id',
                'canal_id',
                'usuario_admin_id',
                'prioridad_id',
                'desde',
                'hasta',
                'con_derivados',
                'con_citas',
                'con_hijos',
                'perPage',
                'creado_por_id',
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset([
            'buscar',
            'unidad_negocio_id',
            'proyecto_id',
            'estado_id',
            'area_id',
            'solicitud_id',
            'sub_tipo_solicitud_id',
            'canal_id',
            'prioridad_id',
            'con_derivados',
            'con_citas',
            'con_hijos',
            'creado_por_id',
        ]);

        // Seteamos a string vacío en lugar de null (reset default)
        // para que mount() no vuelva a aplicar los filtros automáticos
        $this->usuario_admin_id = '';
        $this->desde = '';
        $this->hasta = '';

        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('ticket.accion-exportar-filtro');

        return Excel::download(
            new TicketExport(
                $this->buscar,
                $this->unidad_negocio_id,
                $this->proyecto_id,
                $this->estado_id,
                $this->area_id,
                $this->solicitud_id,
                $this->sub_tipo_solicitud_id,
                $this->canal_id,
                $this->usuario_admin_id,
                $this->prioridad_id,
                $this->desde,
                $this->hasta,
                $this->con_derivados,
                $this->con_citas,
                $this->con_hijos,
                false,
                $this->perPage,
                $this->getPage(),
                $this->creado_por_id
            ),
            'tickets_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('ticket.accion-exportar-todo');

        return Excel::download(
            new TicketExport(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $this->desde,
                $this->hasta,
                '',
                '',
                '',
                true
            ),
            'tickets_todo.xlsx'
        );
    }

    public function render()
    {
        $items = Ticket::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('dni', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->estado_id, fn($q) => $q->where('estado_ticket_id', $this->estado_id))
            ->when($this->area_id, fn($q) => $q->where('area_id', $this->area_id))
            ->when($this->solicitud_id, fn($q) => $q->where('tipo_solicitud_id', $this->solicitud_id))
            ->when($this->sub_tipo_solicitud_id, fn($q) => $q->where('sub_tipo_solicitud_id', $this->sub_tipo_solicitud_id))
            ->when($this->canal_id, fn($q) => $q->where('canal_id', $this->canal_id))
            ->when($this->usuario_admin_id, fn($q) => $q->where('gestor_id', $this->usuario_admin_id))
            ->when($this->creado_por_id, fn($q) => $q->where('created_by', $this->creado_por_id))
            ->when(
                $this->desde,
                fn($q) =>
                $q->whereDate('created_at', '>=', $this->desde)
            )
            ->when(
                $this->hasta,
                fn($q) =>
                $q->whereDate('created_at', '<=', $this->hasta)
            )
            ->when($this->prioridad_id, fn($q) => $q->where('prioridad_ticket_id', $this->prioridad_id))
            ->when(
                $this->con_derivados == '1',
                fn($q) => $q->whereHas('derivados')
            )
            ->when(
                $this->con_derivados == '0',
                fn($q) => $q->whereDoesntHave('derivados')
            )
            ->when(
                $this->con_citas == '1',
                fn($q) => $q->whereHas('citas')
            )
            ->when(
                $this->con_citas == '0',
                fn($q) => $q->whereDoesntHave('citas')
            )
            ->when(
                $this->con_hijos == '1',
                fn($q) => $q->whereNotNull('ticket_padre_id')
            )
            ->when(
                $this->con_hijos == '0',
                fn($q) => $q->whereNull('ticket_padre_id')
            )
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        $unreadTicketIds = auth()->user()->unreadNotifications()
            ->where('type', 'App\Notifications\TicketActualizadoNotification')
            ->get()
            ->pluck('data.ticket_id')
            ->unique()
            ->toArray();

        return view('livewire.erp.atc.ticket.ticket-lista', [
            'items' => $items,
            'unreadTicketIds' => $unreadTicketIds
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
