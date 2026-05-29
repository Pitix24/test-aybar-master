<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaConfirmacion;
use App\Mail\EntregaFest\CitaConfirmacionMail;
use App\Support\EntregaFestCelular;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestCitaConfirmacionN8N
{
    public function handle(EntregaFestCitaConfirmacion $event): void
    {
        $prospecto = $event->prospecto->load(['entregaFest', 'proyecto.unidadNegocio', 'historialComunicaciones']);
        $evento = $prospecto->entregaFest;

        // Solo procesamos si ya tiene fecha de firma agendada
        if (!$prospecto->fecha_firma) {
            Log::channel('entrega-fest')->info("[CITA-CONFIRMACION] Saltado para #{$prospecto->id} (No tiene fecha de firma)");
            return;
        }

        // 1. Verificamos si ya se enviaron comunicaciones para esta etapa
        $etapa = 'cita-confirmacion';
        $plantilla = $evento->plantillas()->where('tipo', $etapa)->first();
        if ($plantilla) {
            $etapa = $plantilla->tipo;
        }

        $yaFueEmail = $prospecto->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

        $yaFueWhatsapp = $prospecto->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

        // 2. Generamos el HTML del Email usando la mailable
        $mail = new CitaConfirmacionMail($prospecto);

        // 3. Preparamos el contacto base
        $contacto = [
            'id' => $prospecto->id,
            'nombres' => $prospecto->nombres,
            'email' => $prospecto->email,
            'celular' => EntregaFestCelular::peru($prospecto->celular),
            'dni' => $prospecto->dni,
            'tipo' => 'Propietario',
            'proyecto' => $prospecto->proyecto?->nombre,
            'sede_nombre' => $prospecto->proyecto?->unidadNegocio?->nombre,
            'direccion_sede' => $prospecto->proyecto?->unidadNegocio?->direccion,
            'fecha_firma' => $prospecto->fecha_firma,
            'fecha_firma_formateada' => $mail->fechaFormateada,
            'html' => $mail->render(),
            'enviar_email' => !$yaFueEmail,
            'enviar_whatsapp' => !$yaFueWhatsapp,
        ];

        // 4. ENVIAMOS EL PAQUETE A N8N
        $this->enviarCitaConfirmacionAN8N($contacto, $evento, $plantilla, $etapa);
    }

    /**
     * Envía la notificación de cita confirmada a n8n.
     */
    private function enviarCitaConfirmacionAN8N($contacto, $evento, $plantilla, $etapa)
    {
        try {
            Http::post(config('services.n8n.entregafest.cita_confirmacion'), [
                'contacto' => $contacto,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '🎉 ¡Cita de Firma Confirmada!: ' . $evento->nombre,
                    'subtitulo' => $plantilla?->subtitulo ?? "Tu cita está agendada para: {$contacto['fecha_firma_formateada']}",
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $plantilla?->link_boton ?? '',
                ],
                'etapa' => $etapa
            ]);

            Log::channel('entrega-fest')->info("[CITA-CONFIRMACION-PAQUETE-N8N] Enviada exitosamente para Prospecto #{$contacto['id']}");
        } catch (\Exception $e) {
            Log::error("[CITA-CONFIRMACION-PAQUETE-N8N] Error: " . $e->getMessage());
        }
    }
}
