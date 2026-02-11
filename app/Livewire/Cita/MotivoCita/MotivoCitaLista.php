<?php

namespace App\Livewire\Cita\MotivoCita;

use App\Models\MotivoCita;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp')]
class MotivoCitaLista extends Component
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
        $items = MotivoCita::where('nombre', 'like', '%' . $this->buscar . '%')
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.cita.motivo-cita.motivo-cita-lista', compact('items'));
    }
}
