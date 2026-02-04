<?php

namespace App\Livewire\Erp\Proyecto;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\GrupoProyecto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProyectosExport;

#[Layout('layouts.erp.layout-erp')]
class ProyectoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    public $unidades_negocios;
    public $grupo_proyectos;

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $grupo_proyecto_id = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 20;

    public function mount()
    {
        $this->unidades_negocios = UnidadNegocio::all();
        $this->grupo_proyectos = GrupoProyecto::all();
    }

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedUnidadNegocioId()
    {
        $this->resetPage();
    }

    public function updatedGrupoProyectoId()
    {
        $this->resetPage();
    }

    public function updatingActivo()
    {
        $this->resetPage();
    }

    public function resetFiltros()
    {
        $this->reset([
            'buscar',
            'unidad_negocio_id',
            'grupo_proyecto_id',
            'activo'
        ]);

        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new ProyectosExport(
                $this->buscar,
                $this->unidad_negocio_id !== '' ? (int) $this->unidad_negocio_id : null,
                $this->grupo_proyecto_id !== '' ? (int) $this->grupo_proyecto_id : null,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'proyectos.xlsx'
        );
    }

    public function render()
    {
        $items = Proyecto::query()
            ->with(['unidadNegocio', 'grupoProyecto'])
            ->when($this->buscar, function ($query) {
                $query->search($this->buscar);
            })
            ->when($this->unidad_negocio_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->grupo_proyecto_id, function ($query) {
                $query->where('grupo_proyecto_id', $this->grupo_proyecto_id);
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.proyecto.proyecto-lista', compact('items'));
    }
}
