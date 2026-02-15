<?php

namespace App\Livewire\Erp\Sistema\Rol;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Lazy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Rol')]
class RolCrear extends Component
{
    public $name;
    public $permissions = [];

    protected function rules()
    {
        return [
            'name' => 'required|unique:roles,name',
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

    public function updated($propertyName)
    {
        if ($propertyName === 'name') {
            $this->name = Str::slug($this->name);
        }
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('rol.crear');

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

            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web',
            ]);

            if (!empty($this->permissions)) {
                $role->syncPermissions($this->permissions);
            }

            DB::commit();

            Log::channel('roles')->info('Rol creado exitosamente', [
                'usuario_id' => auth()->id(),
                'usuario_nombre' => auth()->user()->name,
                'rol_id' => $role->id,
                'rol_nombre' => $role->name,
                'permisos' => $this->permissions,
                'ip' => request()->ip()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Creado',
                'text' => 'El rol se guardó correctamente.'
            ]);

            return redirect()->route('erp.rol.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('roles')->error('Error al crear rol', [
                'usuario_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'datos' => [
                    'name' => $this->name,
                    'permissions' => $this->permissions
                ]
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el rol. Intente nuevamente.'
            ]);
        }
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get()->groupBy('module');

        return view('livewire.erp.sistema.rol.rol-crear', compact('allPermissions'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
