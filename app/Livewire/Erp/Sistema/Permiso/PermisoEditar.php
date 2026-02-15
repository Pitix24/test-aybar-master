<?php

namespace App\Livewire\Erp\Sistema\Permiso;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Permiso')]
class PermisoEditar extends Component
{
    public Permission $permission;
    public $name;
    public $module;

    protected function rules()
    {
        return [
            'name' => 'required|string|unique:permissions,name,' . $this->permission->id,
            'module' => 'required|string|max:100',
        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => 'nombre del permiso',
            'module' => 'módulo',
        ];
    }

    public function mount($id)
    {
        $this->permission = Permission::findOrFail($id);
        $this->name = $this->permission->name;
        $this->module = $this->permission->module;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'name') {
            $this->name = Str::slug($this->name);
        }
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('permiso.editar');

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

            $this->permission->update([
                'name' => $this->name,
                'module' => trim($this->module),
            ]);

            DB::commit();

            Log::channel('permissions')->info('Permiso actualizado', [
                'usuario_id' => auth()->id(),
                'permiso_id' => $this->permission->id,
                'nuevo_nombre' => $this->name,
                'nuevo_modulo' => $this->module,
                'ip' => request()->ip(),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'El permiso se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar permiso: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el permiso.'
            ]);
        }
    }

    #[On('eliminarPermisoOn')]
    public function eliminarPermisoOn()
    {
        $this->authorize('permiso.eliminar');

        try {
            DB::beginTransaction();
            $permisoId = $this->permission->id;
            $permisoNombre = $this->permission->name;

            $this->permission->delete();
            DB::commit();

            Log::channel('permissions')->info('Permiso eliminado', [
                'usuario_id' => auth()->id(),
                'permiso_id' => $permisoId,
                'nombre' => $permisoNombre,
                'ip' => request()->ip(),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El permiso fue eliminado.'
            ]);

            return redirect()->route('erp.permiso.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar permiso: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el permiso.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.sistema.permiso.permiso-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
