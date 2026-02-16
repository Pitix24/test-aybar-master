<?php

namespace App\Livewire\Erp\Cita\MotivoCita;

use App\Models\MotivoCita;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Cita\MotivoCitaExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Motivos de Cita')]
class MotivoCitaLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $desde = '';

    #[Url]
    public $hasta = '';

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'activo', 'perPage', 'desde', 'hasta'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('motivo-cita.exportar-filtro');

        return Excel::download(
            new MotivoCitaExport(
                buscar: $this->buscar,
                activo: $this->activo,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'motivos_cita_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('motivo-cita.exportar-todo');

        return Excel::download(
            new MotivoCitaExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'motivos_cita_total_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = MotivoCita::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%");
                    if (is_numeric($this->buscar)) {
                        $sub->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->activo !== '', fn($q) => $q->where('activo', $this->activo))
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.cita.motivo-cita.motivo-cita-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
