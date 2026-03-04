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
use App\Events\ProspectoBackofficeConforme;
use App\Listeners\EnviarInvitacionesAsistencia;
use App\Events\ProspectoLegalConforme;
use App\Listeners\EnviarLinkFirma;
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

            // ── EntregaFest ──────────────────────────────────────────────────
        ProspectoBackofficeConforme::class => [
            EnviarInvitacionesAsistencia::class,
        ],

        ProspectoLegalConforme::class => [
            EnviarLinkFirma::class,
        ],

        \App\Events\EntregaFestAsistenciaConfirmada::class => [
            \App\Listeners\EnviarNotificacionesAsistenciaConfirmada::class,
        ],

        \App\Events\EntregaFestFirmaRecordatorio::class => [
            \App\Listeners\EnviarRecordatorioFirma::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
