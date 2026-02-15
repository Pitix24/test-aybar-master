<?php

namespace App\Livewire\Erp\Sistema\Menu;

use App\Models\Menu;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;

use App\Exports\Sistema\MenuExport;
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
        $this->perPage = 50;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('menu.exportar-filtro');

        return Excel::download(
            new MenuExport(
                buscar: $this->buscar,
                activo: $this->activo,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'menu_filtrado_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('menu.exportar-todo');

        return Excel::download(
            new MenuExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'menu_completo_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = Menu::query()
            ->whereNull('parent_id')
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%")
                        ->orWhere('id', $this->buscar);
                });
            })
            ->when($this->activo !== '', function ($q) {
                $q->where('activo', $this->activo);
            })
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
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

        return view('livewire.erp.sistema.menu.menu-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
