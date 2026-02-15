<?php

namespace App\Livewire\Erp\Sistema\Permiso;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Permiso')]
class PermisoCrear extends Component
{
    public $name;
    public $module;

    protected function rules()
    {
        return [
            'name' => 'required|string|unique:permissions,name',
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

    public function updated($propertyName)
    {
        if ($propertyName === 'name') {
            $this->name = Str::slug($this->name);
        }
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('permiso.crear');

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

            $permission = Permission::create([
                'name' => $this->name,
                'module' => trim($this->module),
                'guard_name' => 'web',
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Creado',
                'text' => 'El permiso se guardó correctamente.'
            ]);

            return redirect()->route('erp.permiso.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('permissions')->error("[PERMISO] Error al crear permiso: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'name' => $this->name,
                'module' => $this->module,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el permiso. Intente nuevamente.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.sistema.permiso.permiso-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
