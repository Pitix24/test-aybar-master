<?php

namespace App\Livewire\Erp\Permiso;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
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
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->permission->update([
                'name' => $this->name,
                'module' => trim($this->module),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'El permiso se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar permiso: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar el permiso.']);
        }
    }

    #[On('eliminarPermisoOn')]
    public function eliminarPermisoOn()
    {
        try {
            DB::beginTransaction();
            $this->permission->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El permiso fue eliminado.']);
            return redirect()->route('erp.permiso.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar permiso: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar el permiso.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.permiso.permiso-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
