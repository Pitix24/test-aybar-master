<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnidadNegocioExport;
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
    public $perPage = 20;

    #[Url]
    public $activo = '';

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
        abort_unless(auth()->user()->can('unidad-negocio.exportar'), 403);
        return Excel::download(
            new UnidadNegocioExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'unidad-negocios.xlsx'
        );
    }

    public function render()
    {
        $items = UnidadNegocio::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.unidad-negocio.unidad-negocio-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
