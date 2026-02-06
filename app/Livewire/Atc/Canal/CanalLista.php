<?php

namespace App\Livewire\Atc\Canal;

use App\Models\Canal;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CanalExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class CanalLista extends Component
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
            new CanalExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'canales.xlsx'
        );
    }

    public function render()
    {
        $items = Canal::query()
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

        return view('livewire.atc.canal.canal-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
