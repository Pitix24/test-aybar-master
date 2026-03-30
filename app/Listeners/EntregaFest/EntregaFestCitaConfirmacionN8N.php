<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaConfirmacion;
use App\Mail\EntregaFest\CitaConfirmacionMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestCitaConfirmacionN8N
{
    public function handle(EntregaFestCitaConfirmacion $event): void
    {
        $prospecto = $event->prospecto->fresh(['entregaFest', 'proyecto']);
        $evento = $prospecto->entregaFest;

        // Solo procesamos si ya tiene fecha de firma agendada
        if (!$prospecto->fecha_firma) {
            return;
        }

        // 1. Generamos el HTML del Email usando la mailable
        // Esto le permite a n8n tener el cuerpo del correo ya renderizado
        $mail = new CitaConfirmacionMail($prospecto);

        // 2. Preparamos el contacto base
        $contacto = [
            'id' => $prospecto->id,
            'nombres' => $prospecto->nombres,
            'email' => $prospecto->email,
            'celular' => $prospecto->celular,
            'dni' => $prospecto->dni,
            'tipo' => 'Propietario',
            'proyecto' => $prospecto->proyecto?->nombre,
            'fecha_firma' => $prospecto->fecha_firma,
            'fecha_firma_formateada' => $mail->fechaFormateada,
            'html' => $mail->render(),
        ];

        // 3. ENVIAMOS EL PAQUETE A N8N
        $plantilla = $evento->plantillas()->where('tipo', 'cita-confirmacion')->first();
        $this->enviarCitaConfirmacionAN8N($contacto, $evento, $plantilla);
    }

    /**
     * Envía la notificación de cita confirmada a n8n.
     */
    private function enviarCitaConfirmacionAN8N($contacto, $evento, $plantilla)
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
                'etapa' => 'cita-confirmacion' // Etapa para historial
            ]);

            Log::channel('entrega-fest')->info("[CITA-CONFIRMACION-PAQUETE-N8N] Enviada exitosamente para Prospecto #{$contacto['id']}");

        } catch (\Exception $e) {
            Log::error("[CITA-CONFIRMACION-PAQUETE-N8N] Error: " . $e->getMessage());
        }
    }
}
