<?php

namespace App\Livewire\Erp\Negocio\Proyecto;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\GrupoProyecto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Negocio\ProyectoExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Proyectos')]
class ProyectoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $grupo_proyecto_id = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $desde = '';

    #[Url]
    public $hasta = '';

    #[Url]
    public $perPage = 20;

    public $unidades_negocios = [];
    public $grupo_proyectos = [];

    public function mount()
    {
        $this->unidades_negocios = UnidadNegocio::select('id', 'nombre')->orderBy('nombre')->get();
        $this->grupo_proyectos = GrupoProyecto::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'unidad_negocio_id', 'grupo_proyecto_id', 'activo', 'desde', 'hasta', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'unidad_negocio_id', 'grupo_proyecto_id', 'activo', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('proyecto.exportar-filtro');

        return Excel::download(
            new ProyectoExport(
                buscar: $this->buscar,
                activo: $this->activo,
                unidad_negocio_id: $this->unidad_negocio_id,
                grupo_proyecto_id: $this->grupo_proyecto_id,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'proyectos_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('proyecto.exportar-todo');

        return Excel::download(
            new ProyectoExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'proyectos_total_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = Proyecto::query()
            ->with(['unidadNegocio:id,nombre', 'grupoProyecto:id,nombre'])
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%");
                    if (is_numeric($this->buscar)) {
                        $sub->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->unidad_negocio_id !== '', fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->grupo_proyecto_id !== '', fn($q) => $q->where('grupo_proyecto_id', $this->grupo_proyecto_id))
            ->when($this->activo !== '', fn($q) => $q->where('activo', $this->activo))
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.negocio.proyecto.proyecto-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
