<?php

namespace App\Livewire\Atc\EstadoTicket;

use App\Models\EstadoTicket;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstadoTicketExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Estado de Ticket')]
class EstadoTicketLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 20;

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'activo',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new EstadoTicketExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'estado-tickets.xlsx'
        );
    }

    public function render()
    {
        $items = EstadoTicket::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('activo', $this->activo)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.atc.estado-ticket.estado-ticket-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
