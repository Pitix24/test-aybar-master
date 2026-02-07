<?php

namespace App\Livewire\Web\Sesion;

use App\Events\UsuarioRegistrado;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
class ClienteRegistrarLivewire extends Component
{
    public $dni;
    public $cliente_encontrado = null;

    public $email;
    public $password;
    public $password_confirmation;

    public $politica_uno = false;
    public $politica_dos = false;

    protected $rules = [
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'politica_uno' => 'accepted',
        'politica_dos' => 'nullable',
    ];

    protected $messages = [
        'politica_uno.accepted' => 'Debes aceptar la política de privacidad.',
        'politica_dos.accepted' => 'Debes aceptar los términos y condiciones.',
    ];

    public function buscarCliente()
    {
        $this->cliente_encontrado = null;

        if (!$this->dni) {
            session()->flash('error', 'Debe ingresar un DNI.');
            return;
        }

        $existingCliente = Cliente::where('dni', $this->dni)->first();

        if ($existingCliente) {
            session()->flash(
                'error',
                "Ya existe una cuenta asociada a este DNI. Tu correo registrado es: {$existingCliente->email}. Recupera tu contraseña."
            );
            return;
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->get("https://aybarcorp.com/slin/cliente/{$this->dni}");

        if ($response->failed()) {
            session()->flash(
                'error',
                'No se pudo validar el DNI en este momento. Intente más tarde.'
            );
            return;
        }

        $cliente = $response->json();

        if (
            isset($cliente['error']) &&
            $cliente['error'] === true
        ) {
            session()->flash(
                'error',
                $cliente['message'] ?? 'Error consultando cliente.'
            );
            return;
        }

        if (!isset($cliente['correo'])) {
            session()->flash(
                'error',
                'La información del cliente es inválida.'
            );
            return;
        }

        session()->flash('status', 'Ahora sí puedes crear tu cuenta');

        $this->cliente_encontrado = $cliente;
    }

    public function registrar()
    {
        if (!$this->cliente_encontrado) {
            session()->flash('error', 'Debe validar su DNI antes de registrarse.');
            return;
        }

        if (strcasecmp(
            trim($this->cliente_encontrado['correo']),
            trim($this->email)
        ) !== 0) {
            session()->flash('error', 'Su correo no coincide con nuestra base de datos.');
            return;
        }

        $this->validate();

        DB::transaction(function () use (&$user) {

            $user = User::create([
                'name' => $this->cliente_encontrado['apellidos_nombres'] ?? $this->email,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'politica_uno' => $this->politica_uno,
                'politica_dos' => $this->politica_dos,
                'rol' => 'cliente',
                'activo' => true,
            ]);

            Cliente::create([
                'user_id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
                'telefono_principal' => $this->cliente_encontrado['telefono'] ?? null,
                'dni' => $this->dni,
            ]);
        });

        Auth::login($user);

        event(new UsuarioRegistrado($user));

        return redirect()->route('verification.notice');
    }

    public function render()
    {
        return view('livewire.web.sesion.registrar-cliente-crear-livewire');
    }
}
