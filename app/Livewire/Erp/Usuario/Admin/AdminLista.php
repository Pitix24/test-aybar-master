<?php

namespace App\Livewire\Erp\Usuario\Admin;

use App\Models\User;
use App\Exports\Usuario\AdminsExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Usuarios Administrativos')]
class AdminLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $role_id = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $desde = '';

    #[Url]
    public $hasta = '';

    public $roles = [];

    public function mount()
    {
        $this->roles = Role::select('id', 'name')->orderBy('name')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'role_id', 'activo', 'perPage', 'desde', 'hasta'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'role_id', 'activo', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('admin.exportar-filtro');

        return Excel::download(
            new AdminsExport(
                buscar: $this->buscar,
                role_id: $this->role_id,
                activo: $this->activo,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'usuarios_admin_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('admin.exportar-todo');

        return Excel::download(
            new AdminsExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'usuarios_admin_completos_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = User::query()
            ->where('rol', 'admin')
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->buscar}%")
                        ->orWhere('email', 'like', "%{$this->buscar}%")
                        ->orWhere('id', $this->buscar);
                });
            })
            ->when($this->role_id !== '', function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->where('id', $this->role_id);
                });
            })
            ->when($this->activo !== '', function ($q) {
                $q->where('activo', $this->activo);
            })
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.usuario.admin.admin-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
