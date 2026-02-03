<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    public $perPage = 20;

    public function updatedBuscar()
    {
        $this->resetPage();
    }

    public function resetFiltros()
    {
        $this->reset(['buscar']);

        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $items = UnidadNegocio::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('razon_social', 'like', '%' . $this->buscar . '%')
                        ->orWhere('ruc', 'like', '%' . $this->buscar . '%')
                        ->orWhere('slin_id', 'like', '%' . $this->buscar . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.unidad-negocio.unidad-negocio-lista', compact('items'));
    }
}
