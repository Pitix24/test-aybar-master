<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFest;
use App\Models\EntregaFestMopTarea;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tareas MOP - Entrega Fest')]
class MopTareaLista extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url]
    public $fase = '';

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $perPage = 20;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

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
        $items = EntregaFestMopTarea::with('user')
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->fase, fn($q) => $q->where('fase', $this->fase))
            ->when($this->buscar, fn($q) => $q
                ->where('titulo', 'like', "%{$this->buscar}%")
                ->orWhere('instruccion', 'like', "%{$this->buscar}%"))
            ->orderBy('fase')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.mop.mop-tarea-lista', compact('items'));
    }
}
