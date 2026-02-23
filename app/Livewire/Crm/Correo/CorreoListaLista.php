<?php

namespace App\Livewire\Crm\Correo;

use App\Models\CorreoLista;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp')]
#[Title('Listas de Contactos')]
class CorreoListaLista extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $listas = CorreoLista::withCount('contactos')
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.crm.correo.correo-lista-lista', [
            'listas' => $listas
        ]);
    }
}
