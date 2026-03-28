<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Mail\EntregaFest\AsistenciaConfirmacionMail;
use App\Mail\EntregaFest\AsistenciaInvitacionCopropietarioMail;
use App\Mail\EntregaFest\AsistenciaInvitacionPropietarioMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestAsistenciaConfirmacionN8N
{
    /**
     * Handle the event.
     */
    public function handle(EntregaFestAsistenciaConfirmacion $event): void
    {
        $invitado = $event->invitado->load(['prospecto', 'copropietario', 'entregaFest']);
        $evento = $invitado->entregaFest;

        // Solo procesamos si el invitado confirmó su asistencia
        if (!$invitado->confirmado) {
            return;
        }

        // 1. Obtenemos el link dinámico desde la mailable original de invitación
        $linkTicket = $invitado->prospecto_entrega_fest_id
            ? (new AsistenciaInvitacionPropietarioMail($invitado->prospecto))->link
            : (new AsistenciaInvitacionCopropietarioMail($invitado->copropietario))->link;

        // 2. Generamos el HTML del Ticket (para que n8n lo envíe por email)
        $mailTicket = new AsistenciaConfirmacionMail($invitado);

        // 3. Preparamos el contacto base
        $contacto = [
            'id' => $invitado->id,
            'nombres' => $invitado->nombre_completo,
            'email' => $invitado->email,
            'celular' => $invitado->celular,
            'dni' => $invitado->dni,
            'tipo' => $invitado->tipo, // 'Titular' o 'Copropietario'
            'lote' => $invitado->lote,
            'link' => $linkTicket, // Extraído dinámicamente de la Mailable
            'transporte' => $invitado->transporte,
            'acompanantes' => $invitado->cantidad_acompanantes_permitidos,
            'observaciones' => $invitado->observaciones_asistencia,
            'html' => $mailTicket->render(),
        ];

        // 4. DISPARO 1: Confirmación de Asistencia (Ticket)
        $plantillaConf = $evento->plantillas()->where('tipo', 'asistencia-confirmacion')->first();
        $this->enviarAsistenciaConfirmacionAN8N($contacto, $evento, $plantillaConf);

        // 5. DISPARO 2: Instrucciones del Evento
        $plantillaInst = $evento->plantillas()->where('tipo', 'instrucciones')->first();
        $this->enviarInstruccionesAN8N($contacto, $evento, $plantillaInst);
    }

    /**
     * Envía la notificación de asistencia confirmada (Ticket) a n8n.
     */
    private function enviarAsistenciaConfirmacionAN8N($contacto, $evento, $plantilla)
    {
        try {
            Http::post(config('services.n8n.entregafest.asistencia_confirmacion'), [
                'contacto' => $contacto,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '🎉 ¡Asistencia Confirmada!: ' . $evento->nombre,
                    'subtitulo' => $plantilla?->subtitulo ?? 'Te confirmamos la recepción de tus datos.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $plantilla?->link_boton ?? '',
                ],
                'etapa' => 'asistencia-confirmacion'
            ]);

            Log::channel('entrega-fest')->info("[CONFIRMACION-REGISTRO-N8N] Enviada para {$contacto['tipo']} #{$contacto['id']}");
        } catch (\Exception $e) {
            Log::error("[CONFIRMACION-REGISTRO-N8N] Error: " . $e->getMessage());
        }
    }

    /**
     * Envía las instrucciones del evento a n8n.
     */
    private function enviarInstruccionesAN8N($contacto, $evento, $plantilla)
    {
        try {
            Http::post(config('services.n8n.entregafest.instrucciones'), [
                'contacto' => $contacto,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '📄 Instrucciones para el Evento',
                    'subtitulo' => $plantilla?->subtitulo ?? 'Te compartimos información importante.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $plantilla?->link_boton ?? '',
                ],
                'etapa' => 'instrucciones'
            ]);

            Log::channel('entrega-fest')->info("[INSTRUCCIONES-REGISTRO-N8N] Enviadas para {$contacto['tipo']} #{$contacto['id']}");
        } catch (\Exception $e) {
            Log::error("[INSTRUCCIONES-REGISTRO-N8N] Error: " . $e->getMessage());
        }
    }
}
