<?php

namespace App\Livewire\Cliente\Perfil;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class PerfilVer extends Component
{
    public $cliente;

    public $telefono_principal;

    public function mount()
    {
        $cliente = Cliente::where('user_id', Auth::id())->firstOrFail();
        $this->cliente = $cliente;
        $this->telefono_principal = $cliente->telefono_principal;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules(), [], $this->validationAttributes());
    }

    protected function rules()
    {
        return [
            'telefono_principal' => 'required|string|max:15',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'telefono_principal' => 'número de celular',
        ];
    }

    public function actualizarDatos()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            session()->flash('error', 'Verifique los errores de los campos resaltados.');
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->cliente->update([
                'telefono_principal' => $this->telefono_principal,
            ]);

            DB::commit();

            session()->flash('success', 'Se ha actualizado su número de celular correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar perfil de cliente: ' . $e->getMessage());
            session()->flash('error', 'No se pudo actualizar el perfil. Intente nuevamente.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.cliente.perfil.perfil-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
