<?php

namespace App\Livewire\Cliente\Perfil;

use Livewire\Component;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CuentaEditar extends Component
{
    public $clave_actual;
    public $clave_nueva;

    public function actualizarClave()
    {
        $rules = [
            'clave_actual' => 'required|string',
            'clave_nueva' => 'required|string|min:8',
        ];

        $messages = [
            'clave_actual.required' => 'La :attribute es obligatoria.',
            'clave_nueva.required' => 'La :attribute es obligatoria.',
            'clave_nueva.min' => 'La :attribute debe tener al menos :min caracteres.',
        ];

        $validationAttributes = [
            'clave_actual' => 'contraseña actual',
            'clave_nueva' => 'nueva contraseña',
        ];

        $this->validate($rules, $messages, $validationAttributes);

        $cliente = Cliente::where('user_id', Auth::id())->firstOrFail();

        if (!Hash::check($this->clave_actual, $cliente->user->password)) {
            $this->addError('clave_actual', 'La contraseña actual no es correcta.');
            return;
        }

        $cliente->user->update([
            'password' => bcrypt($this->clave_nueva),
        ]);

        $this->reset(['clave_actual', 'clave_nueva']);

        session()->flash('success', 'Contraseña actualizada correctamente.');
    }

    public function render()
    {
        return view('livewire.cliente.perfil.cuenta-editar');
    }
}
