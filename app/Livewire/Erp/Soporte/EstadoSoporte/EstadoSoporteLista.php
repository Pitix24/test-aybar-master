<?php

namespace App\Livewire\Erp\Soporte\EstadoSoporte;

use App\Models\Erp\Soporte\EstadoSoporte;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Estados de Soporte')]
class EstadoSoporteLista extends Component
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
        if (in_array($property, ['buscar', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $items = EstadoSoporte::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%");
                    if (is_numeric($this->buscar)) {
                        $sub->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->activo !== '', fn($q) => $q->where('activo', $this->activo))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.soporte.estado-soporte.estado-soporte-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
