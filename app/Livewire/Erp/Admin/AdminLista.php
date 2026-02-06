<?php

namespace App\Livewire\Erp\Admin;

use App\Models\User;
use App\Exports\AdminsExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Usuarios Admin')]
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

    public $roles = [];

    public function mount()
    {
        $this->roles = Role::select('id', 'name')->orderBy('name')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'role_id', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'role_id', 'activo']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new AdminsExport(
                $this->buscar,
                $this->role_id,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'usuarios_admin.xlsx'
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
            ->when($this->role_id !== '', function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('id', $this->role_id);
                });
            })
            ->when($this->activo !== '', function ($q) {
                $q->where('activo', $this->activo);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.admin.admin-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
