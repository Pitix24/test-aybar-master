<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class ClienteLoginListener
{
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        if (isset($user->rol) && $user->rol === 'cliente') {
            Session::flash(
                'bienvenida_cliente',
                'Bienvenido(a) al Portal del Cliente'
            );
        }
    }
}
