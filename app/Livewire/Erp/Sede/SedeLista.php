<?php

namespace App\Livewire\Erp\Sede;

use App\Models\Sede;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SedeExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class SedeLista extends Component
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
            new SedeExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'sedes.xlsx'
        );
    }

    public function render()
    {
        $items = Sede::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('direccion', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('activo', $this->activo)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.sede.sede-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
