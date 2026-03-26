<?php

namespace App\Listeners;

use App\Events\EntregaFestAsistenciaConfirmada;
use App\Mail\EntregaFest\TicketAsistenciaMail;
use App\Mail\EntregaFest\AsistenciaLinkMail;
use App\Mail\EntregaFest\AsistenciaLinkCopropietarioMail;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EnviarNotificacionesAsistenciaConfirmada
{
    /**
     * Handle the event.
     */
    public function handle(EntregaFestAsistenciaConfirmada $event): void
    {
        $invitado = $event->invitado->load(['prospecto', 'copropietario', 'entregaFest']);
        $evento = $invitado->entregaFest;

        // Solo procesamos si el invitado confirmó su asistencia
        if (!$invitado->confirmado) {
            return;
        }

        // 1. Buscamos la plantilla específica para confirmación de asistencia
        $plantilla = $evento->plantillas()->where('tipo', 'asistencia-confirmacion')->first();

        // 2. Generamos el HTML del Ticket (para que n8n lo envíe por email)
        $mailTicket = new TicketAsistenciaMail($invitado);

        // 3. Obtenemos el link dinámico desde la mailable original de invitación
        // (Es el mismo link, que ahora mostrará el ticket al estar ya confirmado)
        $linkTicket = $invitado->prospecto_entrega_fest_id
            ? (new AsistenciaLinkMail($invitado->prospecto))->link
            : (new AsistenciaLinkCopropietarioMail($invitado->copropietario))->link;

        // 4. Preparamos el contacto (puede ser Prospecto Titular o Copropietario automáticamente)
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
            'html' => $mailTicket->render(), // Renderizado de la plantilla de Ticket
        ];

        // 5. ENVIAMOS EL WEBHOOK A N8N
        $this->enviarConfirmacionAN8N($contacto, $evento, $plantilla);
    }

    /**
     * Envía la notificación de registro exitoso a n8n.
     */
    private function enviarConfirmacionAN8N($contacto, $evento, $plantilla)
    {
        try {
            Http::post(config('services.n8n.webhook_entrega_fest_invitacion_confirmacion'), [
                'contacto' => $contacto,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '🎉 ¡Registro Exitoso!: ' . $evento->nombre,
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
}
