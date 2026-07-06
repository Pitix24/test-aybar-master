<?php

namespace App\Livewire\Erp\Sistema\Rol;

use App\Models\Area;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    public \App\Models\Rol $role;
    public $name = '';
    public $permissions = [];
    public $area_id = '';
    public $upper_id = '';

    protected function rules()
    {
        return [
            'name' => [
                'required',
                Rule::unique('roles', 'name')->ignore($this->role->id),
            ],
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

    public function mount($id)
    {
        $this->role = \App\Models\Rol::findOrFail($id);
        $this->name = $this->role->name;
        $this->permissions = $this->role->permissions->pluck('name')->toArray();
        $this->area_id = $this->role->area_id ? (string) $this->role->area_id : '';
        $this->upper_id = $this->role->upper_id ? (string) $this->role->upper_id : '';
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'name' && $this->role->name !== 'super-admin') {
            $this->name = Str::slug($this->name);
        }
        if ($propertyName === 'area_id') {
            $this->upper_id = '';
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

            $newUpperId = $this->upper_id !== '' ? (int) $this->upper_id : null;
            $jerarquiaService = app(\App\Services\JerarquiaService::class);
            if (!$jerarquiaService->validarSinCiclos($this->role->id, $newUpperId)) {
                $this->dispatch('alertaLivewire', [
                    'type' => 'error',
                    'title' => 'Error de Jerarquía',
                    'text' => 'No puedes seleccionar un rol descendiente como superior (ciclo jerárquico).'
                ]);
                throw ValidationException::withMessages([
                    'upper_id' => 'Esta asignación genera un ciclo jerárquico.',
                ]);
            }

            $this->role->update([
                'name' => $this->name,
                'area_id' => $this->area_id !== '' ? $this->area_id : null,
                'upper_id' => $newUpperId,
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
                'usuario_id' => Auth::id(),
                'rol_id' => $this->role->id,
                'name' => $this->name,
                'area_id' => $this->area_id,
                'upper_id' => $this->upper_id,
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
        $rolNombre = $this->role->name;

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
                'usuario_id' => Auth::id(),
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
        $areas = Area::where('activo', true)->orderBy('nombre')->get();

        // Obtener roles disponibles excluyendo el rol actual y sus descendientes
        $currentRolId = $this->role->id;

        $rolesDisponibles = \App\Models\Rol::query()
            ->where('id', '<>', $currentRolId)
            ->when(
                $this->area_id !== '' && $this->area_id !== null,
                fn($q) => $q->where('area_id', (int) $this->area_id)
            )
            ->orderBy('name')
            ->get();

        $rolesDisponibles = $rolesDisponibles->filter(function ($r) {
            return !$this->role->esAntepasadoDe($r);
        });

        return view('livewire.erp.sistema.rol.rol-editar', compact('allPermissions', 'areas', 'rolesDisponibles'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
