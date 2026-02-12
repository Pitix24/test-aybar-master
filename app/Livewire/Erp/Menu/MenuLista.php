<?php

namespace App\Livewire\Erp\Menu;

use App\Models\Menu;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;

use App\Exports\MenuExport;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Gestión de Menú')]
class MenuLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 50;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo']);
        $this->perPage = 50;
        $this->resetPage();
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('menu.exportar'), 403);

        return Excel::download(
            new MenuExport($this->buscar, $this->activo, $this->perPage, $this->getPage()),
            'menu_erp_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = Menu::query()
            ->whereNull('parent_id')
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when($this->activo !== '', function ($q) {
                $q->where('activo', $this->activo);
            })
            ->with([
                'submenus' => function ($query) {
                    $query->with([
                        'submenus' => function ($q) {
                            $q->with('submenus');
                        }
                    ]);
                }
            ])
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.menu.menu-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}

