<?php

namespace App\Livewire\Erp\Sistema\Rol;

use App\Models\Area;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    public $area_id = '';
    public $upper_id = '';

    protected function rules()
    {
        return [
            'name' => 'required|unique:roles,name',
            'area_id' => 'nullable|integer|exists:areas,id',
            'upper_id' => 'nullable|integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => 'nombre del rol',
            'area_id' => 'área',
            'upper_id' => 'rol superior',
            'permissions' => 'permisos',
        ];
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'name') {
            $this->name = Str::slug($this->name);
        }
        if ($propertyName === 'area_id') {
            $this->upper_id = '';
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
                'area_id' => $this->area_id !== '' ? $this->area_id : null,
                'upper_id' => $this->upper_id !== '' ? $this->upper_id : null,
            ]);

            if (!empty($this->permissions)) {
                $role->syncPermissions($this->permissions);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Creado',
                'text' => 'El rol se guardó correctamente.'
            ]);

            return redirect()->route('erp.rol.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('roles')->error("[ROL] Error al crear rol: " . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'datos' => [
                    'name' => $this->name,
                    'area_id' => $this->area_id,
                    'upper_id' => $this->upper_id,
                    'permissions' => $this->permissions
                ],
                'trace' => $e->getTraceAsString()
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
        $areas = Area::where('activo', true)->orderBy('nombre')->get();

        $rolesDisponibles = \App\Models\Rol::query()

            ->when($this->area_id !== '' && $this->area_id !== null, fn($q) => $q->where('area_id', (int) $this->area_id))
            ->orderBy('name')
            ->get();

        return view('livewire.erp.sistema.rol.rol-crear', compact('allPermissions', 'areas', 'rolesDisponibles'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
