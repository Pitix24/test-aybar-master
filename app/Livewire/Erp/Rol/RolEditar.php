<?php

namespace App\Livewire\Erp\Rol;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class RolEditar extends Component
{
    public Role $role;
    public $name = '';
    public $permissions = [];

    protected function rules()
    {
        return [
            'name' => 'required|unique:roles,name,' . $this->role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
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
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->role->update([
                'name' => $this->name,
            ]);

            $this->role->syncPermissions($this->permissions);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizo correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar rol: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarRolOn')]
    public function eliminarRolOn()
    {
        try {
            if ($this->role->name === 'super-admin') {
                $this->dispatch('alertaLivewire', ['title' => 'Acción Protegida', 'text' => 'No puedes eliminar el rol super-admin.']);
                return;
            }

            DB::beginTransaction();
            $this->role->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se elimino correctamente.']);
            return redirect()->route('erp.rol.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar rol: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get()->groupBy(function ($item) {
            return explode('.', $item->name)[0];
        });

        return view('livewire.erp.rol.rol-editar', compact('allPermissions'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
