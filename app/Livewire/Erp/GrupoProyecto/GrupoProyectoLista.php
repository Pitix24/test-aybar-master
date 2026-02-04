<?php

namespace App\Livewire\Erp\GrupoProyecto;

use App\Models\GrupoProyecto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.erp.layout-erp')]
class GrupoProyectoLista extends Component
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
        $items = GrupoProyecto::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.grupo-proyecto.grupo-proyecto-lista', compact('items'));
    }
}
