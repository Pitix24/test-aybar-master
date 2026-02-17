<?php

namespace App\Livewire\Erp\Usuario\Cliente;

use App\Models\Cliente;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Cliente Consultar')]
class ClienteConsultar extends Component
{
    public $dni;
    public $existingCliente;
    public $email;
    public $mostrar_form_email = false;
    public $cliente_encontrado = null;
    public $razones_sociales = [];

    public function mount($dni = null)
    {
        if ($dni) {
            $this->dni = $dni;
            $this->buscarCliente();
        }
    }

    public function buscarCliente()
    {
        $this->resetAntesDeBuscar();

        try {
            $this->validate([
                'dni' => 'required',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://aybarcorp.com/slin/cliente/{$this->dni}");
        } catch (Exception $e) {
            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No fue posible conectarse con el servicio.',
            ]);
            return;
        }

        if (!$response->ok()) {
            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No se encontro cliente en SLIN',
            ]);
            return;
        }

        $cliente = $response->json();

        if (!is_array($cliente)) {
            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'Respuesta inválida del servicio.',
            ]);
            return;
        }

        $this->cliente_encontrado = $cliente;
        $this->email = $this->cliente_encontrado['correo'];
        $this->razones_sociales = $cliente['empresas'] ?? [];

        $this->existingCliente = Cliente::where('dni', $this->dni)->first();

        if (!$this->existingCliente) {
            $this->mostrar_form_email = true;
            session()->flash('info', 'Si deseas registrarlo en el Portal, ingrese un correo.');
        } else {
            $this->mostrar_form_email = false;
            session()->flash('success', 'Cliente ya registrado en el Portal.');
        }
    }

    public function store()
    {
        $this->authorize('cliente.crear');

        try {
            $this->validate([
                'email' => 'required|email|unique:users,email',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        $tempPassword = Str::random(8);

        $user = User::create([
            'name' => $this->cliente_encontrado['apellidos_nombres'] ?? $this->email,
            'email' => $this->email,
            'password' => Hash::make($tempPassword),
            'must_change_password' => true,
            'password_changed_at' => null,
            'politica_uno' => true,
            'rol' => 'cliente',
            'activo' => true,
        ]);

        $cliente_nuevo = Cliente::create([
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'telefono_principal' => $this->cliente_encontrado['telefono'] ?? null,
            'dni' => $this->dni,
        ]);

        Password::sendResetLink(['email' => $user->email]);

        $this->dispatch('alertaLivewire', [
            'title' => 'Creado',
            'text' => 'Cliente registrado. Contraseña temporal enviada al correo ' . $user->email . '',
            'showConfirmButton' => true,
        ]);

        $this->existingCliente = $cliente_nuevo;
        $this->mostrar_form_email = false;
    }

    public function resetAntesDeBuscar()
    {
        $this->reset([
            'cliente_encontrado',
            'razones_sociales',
            'existingCliente',
            'email',
            'mostrar_form_email',
        ]);

        $this->resetValidation();
        session()->forget(['success', 'error', 'info']);
    }

    public function resetFiltros()
    {
        $this->reset([
            'dni',
            'cliente_encontrado',
            'razones_sociales',
            'existingCliente',
            'email',
            'mostrar_form_email',
        ]);

        $this->resetValidation();
        session()->forget(['success', 'error', 'info']);
    }

    public function render()
    {
        return view('livewire.erp.usuario.cliente.cliente-consultar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
