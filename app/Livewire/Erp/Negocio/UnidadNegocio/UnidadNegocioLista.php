<?php

namespace App\Livewire\Erp\Negocio\UnidadNegocio;

use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Negocio\UnidadNegocioExport;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Unidades de Negocio')]
class UnidadNegocioLista extends Component
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
        if (
            in_array($property, [
                'buscar',
                'activo',
                'perPage',
                'desde',
                'hasta'
            ])
        ) {
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
        $this->authorize('unidad-negocio.exportar-filtro');

        return Excel::download(
            new UnidadNegocioExport(
                buscar: $this->buscar,
                activo: $this->activo,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'unidades_negocio_filtradas_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('unidad-negocio.exportar-todo');

        return Excel::download(
            new UnidadNegocioExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'unidades_negocio_completas_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = UnidadNegocio::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->buscar}%")
                        ->orWhere('razon_social', 'like', "%{$this->buscar}%")
                        ->orWhere('ruc', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.negocio.unidad-negocio.unidad-negocio-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
