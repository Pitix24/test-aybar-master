<?php

namespace App\Livewire\Erp\Negocio\Proyecto;

use App\Models\Proyecto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle del Proyecto')]
class ProyectoVer extends Component
{
    public Proyecto $proyecto;

    public function mount($id)
    {
        $this->authorize('proyecto.ver');
        $this->proyecto = Proyecto::with(['unidadNegocio', 'grupoProyecto'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.negocio.proyecto.proyecto-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
