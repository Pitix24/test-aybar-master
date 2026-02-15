<?php

namespace App\Livewire\Erp\Sistema\Rol;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Rol')]
class RolVer extends Component
{
    public Role $role;
    public $permissions = [];

    public function mount($id)
    {
        $this->role = Role::with('permissions')->findOrFail($id);
        $this->permissions = $this->role->permissions->pluck('name')->toArray();
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get()->groupBy('module');

        return view('livewire.erp.sistema.rol.rol-ver', compact('allPermissions'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
