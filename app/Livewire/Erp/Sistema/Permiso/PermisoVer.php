<?php

namespace App\Livewire\Erp\Sistema\Permiso;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Permiso')]
class PermisoVer extends Component
{
    public Permission $permission;

    public function mount($id)
    {
        $this->permission = Permission::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.sistema.permiso.permiso-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
