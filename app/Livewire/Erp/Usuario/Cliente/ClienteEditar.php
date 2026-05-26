<?php

namespace App\Livewire\Erp\Usuario\Cliente;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Cliente Portal')]
class ClienteEditar extends Component
{
    public User $user_model;
    public $name;
    public $email;
    public $dni = '';
    public $telefono_principal;
    public $activo;
    public $direccion;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user_model->id,
            'dni' => 'required|string|max:20',
            'telefono_principal' => 'nullable|string|max:20',
            'activo' => 'boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'dni' => 'DNI',
            'telefono_principal' => 'celular/teléfono',
            'activo' => 'estado',
        ];
    }

    public function mount($id)
    {
        $this->user_model = User::with(['perfilCliente', 'direccion.region', 'direccion.provincia', 'direccion.distrito'])->findOrFail($id);

        $this->name = $this->user_model->name;
        $this->email = $this->user_model->email;
        $this->activo = (bool) $this->user_model->activo;

        $this->dni = $this->user_model->perfilCliente?->dni ?? '';
        $this->telefono_principal = $this->user_model->perfilCliente?->telefono_principal ?? '';
        $this->direccion = $this->user_model->direccion;
    }

    public function update()
    {
        $this->authorize('cliente.editar');

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

            if ($this->user_model->perfilCliente) {
                $this->user_model->perfilCliente->update([
                    'nombre' => trim($this->name),
                    'email' => strtolower(trim($this->email)),
                    'dni' => trim($this->dni),
                    'telefono_principal' => trim($this->telefono_principal),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Los datos del cliente se han actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cliente')->error("[CLIENTE] Error al actualizar cliente: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->user_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el cliente.'
            ]);
        }
    }

    public function enviarRecuperarClave()
    {
        $this->authorize('cliente.enviar-recuperar-clave');

        try {
            $this->validateOnly('email');
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'El correo electrónico es inválido.'
            ]);
            throw $e;
        }

        Password::sendResetLink(['email' => $this->email]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Correo Enviado',
            'text' => 'Se ha enviado un enlace para restablecer la contraseña a ' . $this->email,
        ]);
    }

    #[On('eliminarClienteOn')]
    public function eliminarClienteOn()
    {
        $this->authorize('cliente.eliminar');

        try {
            DB::beginTransaction();
            $this->user_model->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El cliente ha sido eliminado correctamente.'
            ]);

            return redirect()->route('erp.cliente.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cliente')->error("[CLIENTE] Error al eliminar cliente: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->user_model->id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el cliente.'
            ]);
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        return view('livewire.erp.usuario.cliente.cliente-editar');
    }
}
