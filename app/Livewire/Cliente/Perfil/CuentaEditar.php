<?php

namespace App\Livewire\Cliente\Perfil;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class CuentaEditar extends Component
{
    public $clave_actual;
    public $clave_nueva;

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules(), $this->messages(), $this->validationAttributes());
    }

    protected function rules()
    {
        return [
            'clave_actual' => 'required|string',
            'clave_nueva' => 'required|string|min:8',
        ];
    }

    protected function messages()
    {
        return [
            'clave_actual.required' => 'La :attribute es obligatoria.',
            'clave_nueva.required' => 'La :attribute es obligatoria.',
            'clave_nueva.min' => 'La :attribute debe tener al menos :min caracteres.',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'clave_actual' => 'contraseña actual',
            'clave_nueva' => 'nueva contraseña',
        ];
    }

    public function actualizarClave()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            session()->flash('error', 'Verifique los errores de los campos resaltados.');
            throw $e;
        }

        try {
            DB::beginTransaction();

            $cliente = Cliente::where('user_id', Auth::id())->firstOrFail();

            if (!Hash::check($this->clave_actual, $cliente->user->password)) {
                $this->addError('clave_actual', 'La contraseña actual no es correcta.');
                DB::rollBack();
                return;
            }

            $cliente->user->update([
                'password' => bcrypt($this->clave_nueva),
            ]);

            DB::commit();

            $this->reset(['clave_actual', 'clave_nueva']);

            session()->flash('success', 'Contraseña actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar clave de cliente: ' . $e->getMessage());
            session()->flash('error', 'No se pudo actualizar la contraseña. Intente nuevamente.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.cliente.perfil.cuenta-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
