<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\ClienteLoginListener;
use Illuminate\Auth\Events\PasswordReset;
use App\Listeners\PasswordResetListener;
use App\Events\TicketCreado;
use App\Listeners\EnviarCorreoTicketCreado;
use App\Events\UsuarioRegistrado;
use App\Listeners\EnviarCorreoVerificacionUsuario;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            ClienteLoginListener::class,
        ],

        PasswordReset::class => [
            PasswordResetListener::class,
        ],

        UsuarioRegistrado::class => [
            EnviarCorreoVerificacionUsuario::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
