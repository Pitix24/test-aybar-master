<?php

namespace App\Livewire\Erp\Permiso;

use App\Exports\PermisosExport;
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

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new PermisosExport(
                $this->buscar,
                $this->perPage,
                $this->getPage()
            ),
            'permisos.xlsx'
        );
    }

    public function render()
    {
        $items = Permission::query()
            ->when($this->buscar, function ($query) {
                $query->where('name', 'like', '%' . $this->buscar . '%')
                    ->orWhere('module', 'like', '%' . $this->buscar . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.permiso.permiso-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
