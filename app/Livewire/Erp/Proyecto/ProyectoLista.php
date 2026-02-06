<?php

namespace App\Livewire\Erp\Proyecto;

use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\GrupoProyecto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProyectosExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
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
        if (
            in_array($property, [
                'buscar',
                'unidad_negocio_id',
                'grupo_proyecto_id',
                'activo',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'unidad_negocio_id', 'grupo_proyecto_id', 'activo']);
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
            ->with(['unidadNegocio:id,nombre', 'grupoProyecto:id,nombre'])
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when(
                $this->unidad_negocio_id !== '',
                fn($q) =>
                $q->where('unidad_negocio_id', $this->unidad_negocio_id)
            )
            ->when(
                $this->grupo_proyecto_id !== '',
                fn($q) =>
                $q->where('grupo_proyecto_id', $this->grupo_proyecto_id)
            )
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('activo', $this->activo)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.proyecto.proyecto-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
