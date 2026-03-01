<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFestMopPlantilla;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Plantillas MOP - Entrega Fest')]
class MopPlantillaLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $fase = '';

    #[Url]
    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'fase', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'fase']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $items = EntregaFestMopPlantilla::query()
            ->when($this->buscar, fn($q) => $q->where('instruccion', 'like', "%{$this->buscar}%")
                ->orWhere('rol_nombre', 'like', "%{$this->buscar}%"))
            ->when($this->fase, fn($q) => $q->where('fase', $this->fase))
            ->orderBy('fase')
            ->orderBy('prioridad')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.mop.mop-plantilla-lista', compact('items'));
    }
}
