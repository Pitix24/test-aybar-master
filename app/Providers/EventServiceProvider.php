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

        \App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion::class => [
            \App\Listeners\EntregaFest\EntregaFestAsistenciaConfirmacionN8N::class,
        ],

        \App\Events\EntregaFest\EntregaFestInstrucciones::class => [
            \App\Listeners\EntregaFest\EntregaFestInstruccionesN8N::class,
        ],

        \App\Events\EntregaFest\EntregaFestCitaAgendar::class => [
            \App\Listeners\EntregaFest\EntregaFestCitaAgendarN8N::class,
        ],

        \App\Events\EntregaFest\EntregaFestCitaConfirmacion::class => [
            \App\Listeners\EntregaFest\EntregaFestCitaConfirmacionN8N::class,
        ],

        \App\Events\EntregaFest\EntregaFestCitaRecordatorio::class => [
            \App\Listeners\EntregaFest\EntregaFestCitaRecordatorioN8N::class,
        ],

        \App\Events\EntregaFest\EntregaFestPreInvitacion::class => [
            \App\Listeners\EntregaFest\EntregaFestPreInvitacionN8N::class,
        ],

        \App\Events\EntregaFest\EntregaFestAsistenciaInvitacion::class => [
            \App\Listeners\EntregaFest\EntregaFestAsistenciaInvitacionN8N::class,
        ],
        \App\Events\EntregaFest\EntregaFestAsistenciaInvitacionMasivo::class => [
            \App\Listeners\EntregaFest\EntregaFestAsistenciaInvitacionMasivoN8N::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
