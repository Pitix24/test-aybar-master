<?php

namespace App\Listeners;

use App\Events\UsuarioRegistrado;

class EnviarCorreoVerificacionUsuario
{
    public function handle(UsuarioRegistrado $event): void
    {
        $event->user->sendEmailVerificationNotification();
    }
}
