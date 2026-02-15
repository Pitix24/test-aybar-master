<?php

namespace App\Livewire\Erp\Usuario\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Usuario Administrativo')]
class AdminCrear extends Component
{
    public $name;
    public $email;
    public $password;
    public $selected_roles = [];
    public $activo = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'selected_roles' => 'required|array|min:1',
            'activo' => 'required|boolean',
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('admin.crear');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Datos Incompletos',
                'text' => 'Por favor, revise los campos marcados en rojo.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => trim($this->name),
                'email' => strtolower(trim($this->email)),
                'password' => Hash::make($this->password),
                'rol' => 'admin',
                'activo' => $this->activo,
            ]);

            $user->syncRoles($this->selected_roles);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'El usuario ha sido creado correctamente.'
            ]);

            return redirect()->route('erp.admin.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('admins')->error("[USUARIO] Error al crear usuario admin: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error Crítico',
                'text' => 'No se pudo crear el usuario. Se ha registrado el incidente.'
            ]);
        }
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        return view('livewire.erp.usuario.admin.admin-crear', compact('roles'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
