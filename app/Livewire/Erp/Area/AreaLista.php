<?php

namespace App\Livewire\Erp\Area;

use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AreaExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Áreas')]
class AreaLista extends Component
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
        abort_unless(auth()->user()->can('area.exportar'), 403);
        return Excel::download(
            new AreaExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'areas.xlsx'
        );
    }

    public function render()
    {
        $items = Area::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('email_buzon', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('activo', $this->activo)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.area.area-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
