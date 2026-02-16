<?php

namespace App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra;

use App\Models\EstadoSolicitudDigitalizarLetra;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Letra\EstadoSolicitudDigitalizarLetraExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Estados de Digitalización de Letra')]
class EstadoSolicitudDigitalizarLetraLista extends Component
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
        $this->authorize('estado-solicitud-digitalizar-letra.exportar-filtro');

        return Excel::download(
            new EstadoSolicitudDigitalizarLetraExport(
                buscar: $this->buscar,
                activo: $this->activo,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'estados_digitalizacion_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('estado-solicitud-digitalizar-letra.exportar-todo');

        return Excel::download(
            new EstadoSolicitudDigitalizarLetraExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'estados_digitalizacion_total_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = EstadoSolicitudDigitalizarLetra::query()
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

        return view('livewire.erp.letra.estado-solicitud-digitalizar-letra.estado-solicitud-digitalizar-letra-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
