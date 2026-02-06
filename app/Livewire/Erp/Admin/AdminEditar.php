<?php

namespace App\Livewire\Erp\Admin;

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
#[Title('Editar Usuario Admin')]
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
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
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

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'El usuario se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar usuario admin: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar el usuario.']);
        }
    }

    public function updatePassword()
    {
        try {
            $this->validate(['password' => 'required|string|min:8']);
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'La contraseña debe tener al menos 8 caracteres.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->user_model->update([
                'password' => Hash::make($this->password),
            ]);

            DB::commit();

            $this->reset('password');
            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'La contraseña se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar password admin: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar la contraseña.']);
        }
    }

    #[On('eliminarAdminOn')]
    public function eliminarAdminOn()
    {
        try {
            DB::beginTransaction();
            $this->user_model->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El usuario fue eliminado.']);
            return redirect()->route('erp.admin.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar usuario admin: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar el usuario.']);
        }
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        return view('livewire.erp.admin.admin-editar', compact('roles'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
