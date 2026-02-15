<?php

namespace App\Livewire\Erp\Sistema\Rol;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Lazy;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Sistema\RolesExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Lista de Roles')]
class RolLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

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
        $this->reset(['buscar', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('rol.exportar-filtro');

        return Excel::download(
            new RolesExport(
                buscar: $this->buscar,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'roles_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('rol.exportar-todo');

        return Excel::download(
            new RolesExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'roles_reporte_completo.xlsx'
        );
    }

    public function render()
    {
        $items = Role::query()
            ->withCount('permissions')
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.erp.sistema.rol.rol-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
