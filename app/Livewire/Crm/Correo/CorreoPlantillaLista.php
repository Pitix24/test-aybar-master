<?php

namespace App\Livewire\Crm\Correo;

use App\Models\CorreoPlantilla;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp')]
#[Title('Plantillas de Correo')]
class CorreoPlantillaLista extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $plantillas = CorreoPlantilla::where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('asunto', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.crm.correo.correo-plantilla-lista', [
            'plantillas' => $plantillas
        ]);
    }
}
