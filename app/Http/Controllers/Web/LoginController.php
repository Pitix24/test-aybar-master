<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function indexIngresarCliente()
    {
        return view('modules.web.login.ingresar-cliente');
    }

    public function ingresarCliente(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar si el email existe
        $usuario = User::where('email', $request->email)->first();

        if (!$usuario) {
            return back()
                ->withErrors(['email' => 'Este correo no está registrado.'])
                ->withInput();
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $usuario->password)) {
            return back()
                ->withErrors(['password' => 'La contraseña es incorrecta.'])
                ->withInput();
        }

        // Recordarme (checkbox)
        $remember = $request->has('recordarme');

        // Intentar login con recordar
        if (Auth::attempt($request->only('email', 'password'), $remember)) {

            if (Auth::user()->rol === 'cliente') {
                return redirect()->route('cliente.home');
            } elseif (Auth::user()->rol === 'admin') {
                return redirect()->route('admin.home');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Acceso denegado. Solo clientes pueden ingresar aquí.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'No se pudo iniciar sesión. Inténtalo de nuevo.',
        ]);
    }

    public function logoutCliente(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('ingresar.cliente');
    }

    public function indexIngresarAdmin()
    {
        return view('modules.web.login.ingresar-admin');
    }

    public function ingresarAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = User::where('email', $request->email)->first();

        if (!$usuario) {
            return back()
                ->withErrors(['email' => 'Este correo no está registrado.'])
                ->withInput();
        }

        if (!Hash::check($request->password, $usuario->password)) {
            return back()
                ->withErrors(['password' => 'La contraseña es incorrecta.'])
                ->withInput();
        }

        $remember = $request->has('recordarme');

        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            if (Auth::user()->rol === 'admin') {
                return redirect()->route('admin.home');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'Acceso denegado. Solo admin pueden ingresar aquí.',
            ]);
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas.',
        ]);
    }

    public function logoutAdmin(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('ingresar.admin');
    }
}
