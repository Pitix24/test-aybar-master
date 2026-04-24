<?php

namespace App\Livewire\Erp\Marketing\AvanceProyecto;

use App\Models\AvanceProyecto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Avance de Proyecto')]
class AvanceProyectoVer extends Component
{
    public $item;

    public function mount($id)
    {
        $this->item = AvanceProyecto::with(['unidadNegocio', 'grupoProyecto', 'proyecto'])->findOrFail($id);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        return view('livewire.erp.marketing.avance-proyecto.avance-proyecto-ver');
    }
}
