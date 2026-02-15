<?php

namespace App\Livewire\Erp\Sistema\Rol;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Illuminate\Validation\Rule;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Rol')]
class RolEditar extends Component
{
    public Role $role;
    public $name = '';
    public $permissions = [];

    protected function rules()
    {
        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($this->role->id),
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => 'nombre del rol',
            'permissions' => 'permisos',
        ];
    }

    public function mount($id)
    {
        $this->role = Role::findOrFail($id);
        $this->name = $this->role->name;
        $this->permissions = $this->role->permissions->pluck('name')->toArray();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'name' && $this->role->name !== 'super-admin') {
            $this->name = Str::slug($this->name);
        }
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('rol.editar');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->role->update([
                'name' => $this->name,
            ]);

            $this->role->syncPermissions($this->permissions);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'El rol se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('roles')->error("[ROL] Error al actualizar rol: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'rol_id' => $this->role->id,
                'name' => $this->name,
                'permissions' => $this->permissions,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el rol. Intente nuevamente.'
            ]);
        }
    }

    #[On('eliminarRolOn')]
    public function eliminarRolOn()
    {
        $this->authorize('rol.eliminar');

        try {
            if ($this->role->name === 'super-admin') {
                $this->dispatch('alertaLivewire', [
                    'type' => 'warning',
                    'title' => 'Acción Protegida',
                    'text' => 'No puedes eliminar el rol super-admin.'
                ]);
                return;
            }

            DB::beginTransaction();
            $rolNombre = $this->role->name;
            $this->role->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El rol se eliminó correctamente.'
            ]);

            return redirect()->route('erp.rol.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('roles')->error("[ROL] Error al eliminar rol: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'rol_id' => $this->role->id,
                'rol_nombre' => $rolNombre,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el rol. Intente nuevamente.'
            ]);
        }
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get()->groupBy('module');

        return view('livewire.erp.sistema.rol.rol-editar', compact('allPermissions'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
