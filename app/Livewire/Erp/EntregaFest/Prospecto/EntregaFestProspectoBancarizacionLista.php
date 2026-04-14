<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Models\EntregaFest;
use App\Models\ProspectoBancarizacionEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Bancarización del Evento')]
class EntregaFestProspectoBancarizacionLista extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $items = ProspectoBancarizacionEntregaFest::query()
            ->with(['prospecto', 'prospecto.proyecto'])
            ->where('entrega_fest_id', $this->evento->id)
            ->where(function($query) {
                if ($this->buscar) {
                    $query->whereHas('prospecto', function ($q) {
                        $q->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })->orWhere('cuota', 'like', '%' . $this->buscar . '%');
                }
            })
            ->orderBy('fecha_deposito_real', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-bancarizacion-lista', [
            'items' => $items
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
