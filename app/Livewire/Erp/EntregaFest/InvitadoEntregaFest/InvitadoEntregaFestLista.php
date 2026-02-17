<?php

namespace App\Livewire\Erp\EntregaFest\InvitadoEntregaFest;

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
#[Title('Lista de Invitados - Entrega Fest')]
class InvitadoEntregaFestLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $entrega_fest_id = '';

    #[Url(history: true)]
    public $confirmado = '';

    #[Url(history: true)]
    public $asistio = '';

    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'entrega_fest_id', 'confirmado', 'asistio', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'entrega_fest_id', 'confirmado', 'asistio']);
        $this->perPage = 20;
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
        $eventos = EntregaFest::orderBy('fecha_entrega', 'desc')->get();

        $items = InvitadoEntregaFest::query()
            ->with(['entregaFest', 'prospecto', 'asistencia'])
            ->withCount('acompanantes')
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('prospecto', function ($qp) {
                        $qp->where('nombre', 'like', '%' . $this->buscar . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->entrega_fest_id, function ($query) {
                $query->where('entrega_fest_id', $this->entrega_fest_id);
            })
            ->when($this->confirmado !== '', function ($query) {
                $query->where('confirmado', $this->confirmado);
            })
            ->when($this->asistio !== '', function ($query) {
                if ($this->asistio == '1') {
                    $query->whereHas('asistencia');
                } else {
                    $query->whereDoesntHave('asistencia');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.invitado-entrega-fest.invitado-entrega-fest-lista', [
            'items' => $items,
            'eventos' => $eventos,
        ]);
    }
}
