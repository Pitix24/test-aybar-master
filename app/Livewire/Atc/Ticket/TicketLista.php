<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Ticket;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class TicketLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $estado = '';

    #[Url]
    public $prioridad = '';

    #[Url]
    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'estado', 'prioridad', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'estado', 'prioridad']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new TicketExport(
                $this->buscar,
                $this->estado,
                $this->prioridad,
                $this->perPage,
                $this->getPage()
            ),
            'tickets.xlsx'
        );
    }

    public function render()
    {
        $items = Ticket::query()
            ->with(['cliente', 'area', 'estado', 'prioridad', 'gestor'])
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($query) {
                    $query->where('asunto_inicial', 'like', "%{$this->buscar}%")
                        ->orWhere('id', 'like', "%{$this->buscar}%")
                        ->orWhereHas('cliente', function ($client) {
                            $client->where('name', 'like', "%{$this->buscar}%");
                        });
                });
            })
            ->when($this->estado !== '', fn($q) => $q->where('estado_ticket_id', $this->estado))
            ->when($this->prioridad !== '', fn($q) => $q->where('prioridad_ticket_id', $this->prioridad))
            ->latest()
            ->paginate($this->perPage);

        $estados = EstadoTicket::where('activo', true)->get();
        $prioridades = PrioridadTicket::where('activo', true)->get();

        return view('livewire.atc.ticket.ticket-lista', compact('items', 'estados', 'prioridades'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
