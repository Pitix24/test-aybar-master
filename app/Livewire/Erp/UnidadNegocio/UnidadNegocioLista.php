<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnidadNegocioExport;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $perPage = 20;

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function resetFiltros()
    {
        $this->reset(['buscar']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new UnidadNegocioExport(
                $this->buscar,
                $this->perPage,
                $this->getPage()
            ),
            'unidad-negocios.xlsx'
        );
    }

    public function render()
    {
        $items = UnidadNegocio::query()
            ->search($this->buscar)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.unidad-negocio.unidad-negocio-lista', compact('items'));
    }
}
