<?php

namespace App\Livewire\Erp\GrupoProyecto;

use App\Models\GrupoProyecto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GrupoProyectoExport;

#[Layout('layouts.erp.layout-erp')]
class GrupoProyectoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $activo = '';

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatingActivo()
    {
        $this->resetPage();
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new GrupoProyectoExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'grupo-proyectos.xlsx'
        );
    }

    public function render()
    {
        $items = GrupoProyecto::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.grupo-proyecto.grupo-proyecto-lista', compact('items'));
    }
}
