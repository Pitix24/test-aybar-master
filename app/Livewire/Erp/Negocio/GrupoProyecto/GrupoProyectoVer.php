<?php

namespace App\Livewire\Erp\Negocio\GrupoProyecto;

use App\Models\GrupoProyecto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle del Grupo')]
class GrupoProyectoVer extends Component
{
    public GrupoProyecto $grupo;

    public function mount($id)
    {
        $this->authorize('grupo-proyecto.ver');
        $this->grupo = GrupoProyecto::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.negocio.grupo-proyecto.grupo-proyecto-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
