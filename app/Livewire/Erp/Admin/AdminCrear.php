<?php

namespace App\Livewire\Erp\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class AdminCrear extends Component
{
    public $name;
    public $email;
    public $password;
    public $selected_roles = [];
    public $activo = false;

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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
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

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'El usuario se guardó correctamente.']);
            return redirect()->route('erp.admin.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear usuario admin: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear el usuario.']);
        }
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();
        return view('livewire.erp.admin.admin-crear', compact('roles'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
