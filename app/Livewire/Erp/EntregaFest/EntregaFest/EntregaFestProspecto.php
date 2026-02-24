<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\Proyecto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Prospectos del Evento')]
class EntregaFestProspecto extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $perPage = 20;

    // Catálogos
    public $proyectos = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'proyecto_id', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id']);
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $items = ProspectoEntregaFest::query()
            ->with(['proyecto', 'user', 'invitado'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                        ->orWhere('codigo_cliente', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->proyecto_id, function ($query) {
                $query->where('proyecto_id', $this->proyecto_id);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-prospecto', [
            'items' => $items
        ]);
    }
}
