<?php

namespace App\Livewire\Atc\Ticket;

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
use App\Exports\TicketExport;
use Illuminate\Support\Facades\Auth;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tickets')]
class TicketLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $estado_id = '';

    #[Url]
    public $prioridad_id = '';

    #[Url]
    public $usuario_admin_id = '';

    #[Url]
    public $fecha_inicio = '';

    #[Url]
    public $fecha_fin = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    #[Url]
    public $area_id = '';

    #[Url]
    public $solicitud_id = '';

    #[Url]
    public $sub_tipo_solicitud_id = '';

    #[Url]
    public $canal_id = '';

    #[Url]
    public $sedes_id = '';

    #[Url]
    public $con_derivados = '';

    #[Url]
    public $con_citas = '';

    #[Url]
    public $perPage = 20;

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
        $this->usuarios_admin = User::role(['asesor-atc', 'supervisor-atc'])->get();
        $this->prioridades = PrioridadTicket::all();
        $this->usuario_admin_id = Auth::check() ? Auth::id() : '';
        $this->fecha_inicio = now()->toDateString(); // "2025-11-26"
        $this->fecha_fin = now()->toDateString();

        $this->unidades_negocios = UnidadNegocio::all();
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
        $this->sub_tipos_solicitudes = [];

        if ($value) {
            $this->loadSubTipoSolicitudes();
        }
    }

    public function loadSubTipoSolicitudes()
    {
        if (!is_null($this->solicitud_id)) {
            $this->sub_tipos_solicitudes = SubTipoSolicitud::where('tipo_solicitud_id', $this->solicitud_id)->get();
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
                'fecha_inicio',
                'fecha_fin',
                'con_derivados',
                'con_citas',
                'perPage',
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
            'usuario_admin_id',
            'prioridad_id',
            'fecha_inicio',
            'fecha_fin',
            'con_derivados',
            'con_citas',
        ]);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('ticket.exportar'), 403);

        return Excel::download(
            new TicketExport(
                $this->buscar,
                $this->unidades_negocios_id,
                $this->proyecto_id,
                $this->estado_id,
                $this->areas_id,
                $this->solicitudes_id,
                $this->sub_tipo_solicitudes_id,
                $this->canal_id,
                $this->usuario_admin_id,
                $this->prioridad_id,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->con_derivados,
                $this->con_citas,
                $this->perPage,
                $this->getPage()
            ),
            'tickets.xlsx'
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
            ->when(
                $this->fecha_inicio,
                fn($q) =>
                $q->whereDate('created_at', '>=', $this->fecha_inicio)
            )
            ->when(
                $this->fecha_fin,
                fn($q) =>
                $q->whereDate('created_at', '<=', $this->fecha_fin)
            )
            ->when($this->prioridad_id, fn($q) => $q->where('prioridad_ticket_id', $this->prioridad_id))
            ->when(
                $this->con_derivados === '1',
                fn($q) =>
                $q->whereHas('derivados')
            )
            ->when(
                $this->con_derivados === '0',
                fn($q) =>
                $q->whereDoesntHave('derivados')
            )
            ->when(
                $this->con_citas === '1',
                fn($q) =>
                $q->whereHas('citas')
            )
            ->when(
                $this->con_citas === '0',
                fn($q) =>
                $q->whereDoesntHave('citas')
            )
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.atc.ticket.ticket-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
