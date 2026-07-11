<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Events\EntregaFest\EntregaFestInstrucciones;
use App\Mail\EntregaFest\AsistenciaConfirmacionMail;
use App\Mail\EntregaFest\AsistenciaInvitacionCopropietarioMail;
use App\Mail\EntregaFest\AsistenciaInvitacionPropietarioMail;
use App\Support\EntregaFestCelular;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Support\VerificaEventoVigente;

class EntregaFestAsistenciaConfirmacionN8N
{
    use VerificaEventoVigente; // Importamos el trait para verificar si el evento sigue vigente antes de enviar a n8n
    /**
     * Handle the event.
     */
    public function handle(EntregaFestAsistenciaConfirmacion $event): void
    {
        $invitado = $event->invitado->load(['prospecto.historialComunicaciones', 'copropietario.historialComunicaciones', 'entregaFest']);
        $evento = $invitado->entregaFest;

        // 🛑 FILTRO (también previene el dispatch del evento hijo de Instrucciones)
        if (!$this->eventoVigente($evento, 'ASISTENCIA-CONFIRMACION-N8N')) {
            return;
        }

        // 🛑 FILTRO: Si el invitado tiene observación legal, cancelamos el envío
        if ($invitado->observacion_legal) {
            Log::channel('entrega-fest')->warning("[ASISTENCIA-CONFIRMACION] Envío abortado: Invitado #{$invitado->id} tiene restricción/observación legal.");
            return;
        }

        // Definimos la persona (Titular o Copropietario)
        $persona = $invitado->prospecto ?? $invitado->copropietario;

        // Solo procesamos si la persona confirmó su asistencia (invitacion_confirmada = true)
        if ($persona?->invitacion_confirmada) {

            // 1. Obtenemos el link dinámico desde la mailable original de invitación
            $linkTicket = $invitado->prospecto_entrega_fest_id
                ? (new AsistenciaInvitacionPropietarioMail($invitado->prospecto))->link
                : (new AsistenciaInvitacionCopropietarioMail($invitado->copropietario))->link;

            // 2. Generamos el HTML del Ticket (para que n8n lo envíe por email)
            $mailTicket = new AsistenciaConfirmacionMail($invitado);

            // 3. Verificamos si ya se enviaron comunicaciones para esta etapa
            $etapa = 'asistencia-confirmacion';
            $plantillaConf = $evento->plantillas()->where('tipo', $etapa)->first();
            if ($plantillaConf) {
                $etapa = $plantillaConf->tipo;
            }

            $yaFueEmail = $persona->historialComunicaciones
                ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

            $yaFueWhatsapp = $persona->historialComunicaciones
                ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

            // 4. Preparamos el contacto base (ID de Persona)
            $contacto = [
                'id' => $invitado->prospecto_entrega_fest_id ?? $invitado->copropietario_entrega_fest_id,
                'nombres' => $invitado->nombre_completo,
                'email' => $invitado->email,
                'celular' => EntregaFestCelular::peru($invitado->celular),
                'dni' => $invitado->dni,
                'tipo' => $invitado->prospecto_entrega_fest_id ? 'Propietario' : 'Copropietario',
                'lote' => $invitado->lote,
                'link' => $linkTicket,
                'transporte' => $invitado->transporte,
                'acompanantes' => $invitado->cantidad_acompanantes_permitidos,
                'observaciones' => $invitado->observaciones_asistencia,
                'html' => $mailTicket->render(),
                'enviar_email' => !$yaFueEmail,
                'enviar_whatsapp' => !$yaFueWhatsapp,
            ];

            // 5. DISPARO: Confirmación de Asistencia (Ticket) a N8N
            $this->enviarAsistenciaConfirmacionAN8N($contacto, $evento, $plantillaConf, $etapa);

            // 6. EMITIMOS EVENTO HIJO: Instrucciones
            EntregaFestInstrucciones::dispatch($invitado);
        } else {
            Log::channel('entrega-fest')->info("[CONFIRMACION] Saltado para {$invitado->nombre_completo} (No confirmó)");
        }
    }

    /**
     * Envía la notificación de asistencia confirmada (Ticket) a n8n.
     */
    private function enviarAsistenciaConfirmacionAN8N($contacto, $evento, $plantilla, $etapa)
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
                'etapa' => $etapa
            ]);

            Log::channel('entrega-fest')->info("[CONFIRMACION-REGISTRO-N8N] Enviada para {$contacto['tipo']} #{$contacto['id']}");
        } catch (\Exception $e) {
            Log::error("[CONFIRMACION-REGISTRO-N8N] Error: " . $e->getMessage());
        }
    }
}
