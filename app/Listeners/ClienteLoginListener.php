<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class ClienteLoginListener
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user->rol === 'cliente') {
            Session::flash(
                'bienvenida_cliente',
                'Bienvenido(a) al Portal del Cliente'
            );
        }
    }
}
