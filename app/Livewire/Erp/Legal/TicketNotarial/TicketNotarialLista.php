<?php

namespace App\Livewire\Erp\Legal\TicketNotarial;

use App\Exports\Legal\TicketNotarialExport;
use App\Models\EstadoTicket;
use App\Models\Proyecto;
use App\Models\Ticket;
use App\Models\UnidadNegocio;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Cartas Notariales')]
class TicketNotarialLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $unidad_negocio_id = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $estado_id = '';

    #[Url(keep: true)]
    public $gestor_id = '';

    #[Url(keep: true)]
    public $desde = '';

    #[Url(keep: true)]
    public $hasta = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public $estados = [];
    public $gestores = [];
    public $unidades = [];
    public $proyectos = [];

    public function mount(): void
    {
        $this->authorize('ticket-notarial.lista');

        $this->estados = EstadoTicket::query()->orderBy('nombre')->get(['id', 'nombre']);
        $this->gestores = User::query()->where('activo', true)->orderBy('name')->get(['id', 'name']);
        $this->unidades = UnidadNegocio::query()->where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        if ($this->unidad_negocio_id !== '') {
            $this->loadProyectos();
        }
    }

    public function updatedUnidadNegocioId($value): void
    {
        $this->proyecto_id = '';
        $this->proyectos = [];

        if ($value !== '') {
            $this->loadProyectos();
        }
    }

    public function loadProyectos(): void
    {
        $this->proyectos = Proyecto::query()
            ->where('activo', true)
            ->where('unidad_negocio_id', $this->unidad_negocio_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
    }

    public function updated($property): void
    {
        if (in_array($property, ['buscar', 'estado_id', 'gestor_id', 'desde', 'hasta', 'perPage'], true)) {
            $this->resetPage();
        }

        if ($property === 'unidad_negocio_id') {
            $this->resetPage();
        }
    }

    public function resetFiltros(): void
    {
        $this->reset(['buscar', 'estado_id', 'gestor_id', 'desde', 'hasta']);
        $this->unidad_negocio_id = '';
        $this->proyecto_id = '';
        $this->proyectos = [];
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('ticket-notarial.accion-exportar-filtro');

        return Excel::download(
            new TicketNotarialExport(
                $this->buscar,
                $this->unidad_negocio_id,
                $this->proyecto_id,
                $this->estado_id,
                $this->gestor_id,
                $this->desde,
                $this->hasta,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'tickets_notariales_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('ticket-notarial.accion-exportar-todo');

        return Excel::download(
            new TicketNotarialExport(
                '',
                '',
                '',
                '',
                '',
                $this->desde,
                $this->hasta,
                true
            ),
            'tickets_notariales_todo.xlsx'
        );
    }

    public function render()
    {
        $items = Ticket::query()
            ->with(['area', 'estado', 'gestor', 'tipoSolicitud', 'subTipoSolicitud', 'unidadNegocio', 'proyecto'])
            ->cartasNotariales()
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('dni', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_inicial', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_respuesta', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->estado_id !== '', fn($q) => $q->where('estado_ticket_id', $this->estado_id))
            ->when($this->gestor_id !== '', fn($q) => $q->where('gestor_id', $this->gestor_id))
            ->when($this->unidad_negocio_id !== '', fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id !== '', fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->desde !== '', fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta !== '', fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.erp.legal.ticket-notarial.ticket-notarial-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
