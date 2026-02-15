<?php

namespace App\Livewire\Erp\Usuario\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Usuario Administrativo')]
class AdminEditar extends Component
{
    public User $user_model;
    public $name;
    public $email;
    public $password;
    public $selected_roles = [];
    public $activo;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user_model->id,
            'selected_roles' => 'required|array|min:1',
            'activo' => 'boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'selected_roles' => 'roles',
            'activo' => 'estado',
        ];
    }

    public function mount($id)
    {
        $this->user_model = User::findOrFail($id);
        $this->name = $this->user_model->name;
        $this->email = $this->user_model->email;
        $this->activo = (bool) $this->user_model->activo;
        $this->selected_roles = $this->user_model->getRoleNames()->toArray();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'password') {
            $this->validateOnly($propertyName, ['password' => 'required|string|min:8']);
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function update()
    {
        $this->authorize('admin.editar');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Datos Inválidos',
                'text' => 'Verifique los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->user_model->update([
                'name' => trim($this->name),
                'email' => strtolower(trim($this->email)),
                'activo' => $this->activo,
            ]);

            $this->user_model->syncRoles($this->selected_roles);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Los datos del usuario se han actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('usuarios')->error("[USUARIO] Error al actualizar usuario admin: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->user_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el usuario.'
            ]);
        }
    }

    public function updatePassword()
    {
        $this->authorize('cambiar-clave');

        try {
            $this->validate(['password' => 'required|string|min:8']);
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Contraseña Inválida',
                'text' => 'La contraseña debe tener al menos 8 caracteres.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->user_model->update([
                'password' => Hash::make($this->password),
            ]);

            DB::commit();

            $this->reset('password');
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Contraseña Cambiada!',
                'text' => 'La contraseña se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('usuarios')->error("[USUARIO] Error al actualizar password admin: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->user_model->id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la contraseña.'
            ]);
        }
    }

    #[On('eliminarAdminOn')]
    public function eliminarAdminOn()
    {
        $this->authorize('admin.eliminar');

        $userId = $this->user_model->id;
        $userName = $this->user_model->name;

        try {
            DB::beginTransaction();
            $this->user_model->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El usuario ha sido eliminado correctamente.'
            ]);

            return redirect()->route('erp.admin.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('usuarios')->error("[USUARIO] Error al eliminar usuario admin: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $userId,
                'nombre' => $userName,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el usuario.'
            ]);
        }
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        return view('livewire.erp.usuario.admin.admin-editar', compact('roles'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
