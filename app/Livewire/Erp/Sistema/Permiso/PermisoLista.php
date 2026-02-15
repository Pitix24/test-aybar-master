<?php

namespace App\Livewire\Erp\Sistema\Permiso;

use App\Exports\Sistema\PermisosExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Lista de Permisos')]
class PermisoLista extends Component
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
        $this->authorize('permiso.exportar-filtro');

        return Excel::download(
            new PermisosExport(
                buscar: $this->buscar,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'permisos_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('permiso.exportar-todo');

        return Excel::download(
            new PermisosExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'permisos_reporte_completo.xlsx'
        );
    }

    public function render()
    {
        $items = Permission::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->buscar . '%')
                        ->orWhere('module', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.erp.sistema.permiso.permiso-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
