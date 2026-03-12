<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregaFest\EntregaFestExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Entrega Fest')]
class EntregaFestLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $activo = '';

    #[Url(keep: true)]
    public $unidad_negocio_id = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $perPage = 20;

    // Catálogos
    public $unidades_negocios = [];
    public $proyectos = [];

    public function mount()
    {
        $this->unidades_negocios = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();

        if ($this->unidad_negocio_id) {
            $this->loadProyectos();
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
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        }
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'activo', 'unidad_negocio_id', 'proyecto_id', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo', 'unidad_negocio_id', 'proyecto_id']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('entrega-fest.exportar-filtro');

        return Excel::download(
            new EntregaFestExport(
                $this->buscar,
                $this->activo,
                $this->unidad_negocio_id,
                $this->proyecto_id,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'entrega_fest_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('entrega-fest.exportar-todo');

        return Excel::download(
            new EntregaFestExport(
                '',
                '',
                '',
                '',
                true
            ),
            'entrega_fest_todo.xlsx'
        );
    }

    public function render()
    {
        $items = EntregaFest::query()
            ->with(['gestor', 'proyectos'])
            ->withCount(['prospectos', 'invitados'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->when($this->unidad_negocio_id, function ($query) {
                $query->whereHas('proyectos', function ($q) {
                    $q->where('unidad_negocio_id', $this->unidad_negocio_id);
                });
            })
            ->when($this->proyecto_id, function ($query) {
                $query->whereHas('proyectos', function ($q) {
                    $q->where('proyectos.id', $this->proyecto_id);
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-lista', [
            'items' => $items
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
