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
    public $estado_confirmacion = '';

    #[Url(keep: true)]
    public $transporte = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'estado_confirmacion', 'transporte', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'estado_confirmacion', 'transporte']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('entrega-fest.invitados');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EntregaFest\EntregaFestInvitadoExport(
                $this->evento->id,
                $this->buscar,
                $this->estado_confirmacion,
                $this->transporte,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'invitados_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('entrega-fest.invitados');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EntregaFest\EntregaFestInvitadoExport(
                $this->evento->id,
                '',
                '',
                '',
                true
            ),
            'invitados_todo_' . $this->evento->codigo . '.xlsx'
        );
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
            ->with([
                'prospecto.proyecto',
                'copropietario.prospecto.proyecto',
            ])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    // Buscar en titular
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        // Buscar en copropietario
                        ->orWhereHas('copropietario', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        ->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->estado_confirmacion, fn($q) => $q->where('estado_confirmacion', $this->estado_confirmacion))
            ->when($this->transporte, fn($q) => $q->where('transporte', $this->transporte))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-invitado', [
            'items' => $items
        ]);
    }
}
