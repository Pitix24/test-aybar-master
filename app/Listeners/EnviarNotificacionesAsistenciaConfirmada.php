<?php

namespace App\Listeners;

use App\Events\EntregaFestAsistenciaConfirmada;
use App\Mail\EntregaFest\InstruccionesEventoMail;
use App\Mail\EntregaFest\TicketAsistenciaMail;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionesAsistenciaConfirmada implements ShouldQueue
{
    public function __construct(private WhatsappService $whatsapp)
    {
        //
    }

    public function handle(EntregaFestAsistenciaConfirmada $event): void
    {
        $invitado = $event->invitado;
        $evento = $invitado->entregaFest;

        // Solo enviamos si confirmó asistencia
        if (!$invitado->confirmado) {
            return;
        }

        // 1. Ticket por correo
        if ($invitado->email) {
            try {
                Mail::to($invitado->email)->send(new TicketAsistenciaMail($invitado));
            } catch (\Exception $e) {
                Log::error("[NOTIFICACION-CONFIRMADA] Error enviando ticket a {$invitado->email}: " . $e->getMessage());
            }

            // 2. Instrucciones por correo
            try {
                Mail::to($invitado->email)->send(new InstruccionesEventoMail($invitado));
            } catch (\Exception $e) {
                Log::error("[NOTIFICACION-CONFIRMADA] Error enviando instrucciones a {$invitado->email}: " . $e->getMessage());
            }
        }

        // 3. WhatsApp con instrucciones e imagen
        if ($invitado->celular) {
            try {
                $celRaw = preg_replace('/\D/', '', $invitado->celular);
                $celular = strlen($celRaw) === 9 ? '51' . $celRaw : $celRaw;

                $imagenUrl = 'https://plataforma-digital.aybarcorp.com/assets/imagen/construccion-aybar-corp.jpg';
                $caption = "Hola *{$invitado->nombre_completo}*, aquí te compartimos las instrucciones para el evento *{$evento->nombre}*. ¡Te esperamos!";

                $this->whatsapp->sendImage($celular, $imagenUrl, $caption);
            } catch (\Exception $e) {
                Log::error("[NOTIFICACION-CONFIRMADA] Error enviando WhatsApp a {$invitado->celular}: " . $e->getMessage());
            }
        }
    }
}
