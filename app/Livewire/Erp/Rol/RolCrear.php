<?php

namespace App\Livewire\Erp\Rol;

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

    public function updated($propertyName)
    {
        if ($propertyName === 'name') {
            $this->name = Str::slug($this->name);
        }
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        abort_unless(auth()->user()->can('rol.crear'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
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

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardo correctamente.']);
            return redirect()->route('erp.rol.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear rol: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get()->groupBy('module');

        return view('livewire.erp.rol.rol-crear', compact('allPermissions'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
