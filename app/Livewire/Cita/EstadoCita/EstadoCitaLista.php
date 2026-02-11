<?php

namespace App\Livewire\Cita\EstadoCita;

use App\Models\EstadoCita;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp')]
class EstadoCitaLista extends Component
{
    use WithPagination;

    public $buscar = '';
    public $perPage = 15;

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = EstadoCita::where('nombre', 'like', '%' . $this->buscar . '%')
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.cita.estado-cita.estado-cita-lista', compact('items'));
    }
}
