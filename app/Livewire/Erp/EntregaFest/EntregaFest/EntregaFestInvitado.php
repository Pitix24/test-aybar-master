<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Invitados del Evento')]
class EntregaFestInvitado extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $confirmado = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'confirmado', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'confirmado']);
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
        $items = InvitadoEntregaFest::query()
            ->with(['prospecto.proyecto', 'prospecto.user'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombre', 'like', '%' . $this->buscar . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                            ->orWhere('codigo_cliente', 'like', '%' . $this->buscar . '%');
                    })->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->confirmado !== '', function ($query) {
                $query->where('confirmado', $this->confirmado);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-invitado', [
            'items' => $items
        ]);
    }
}
