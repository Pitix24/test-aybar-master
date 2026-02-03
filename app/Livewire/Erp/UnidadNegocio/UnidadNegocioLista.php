<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioLista extends Component
{
    use WithPagination;
    public $buscar = '';
    public $perPage = 20;

    public function render()
    {
        $items = UnidadNegocio::where('nombre', 'like', '%' . $this->buscar . '%')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.unidad-negocio.unidad-negocio-lista', compact('items'));
    }
}
