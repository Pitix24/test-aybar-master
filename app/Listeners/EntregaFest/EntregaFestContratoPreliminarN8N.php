<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestContratoPreliminar;
use App\Events\EntregaFest\EntregaFestCitaAgendar;
use App\Mail\EntregaFest\ContratoPreliminarMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestContratoPreliminarN8N
{
    /**
     * Handle the event.
     */
    public function handle(EntregaFestContratoPreliminar $event): void
    {
        $prospecto = $event->prospecto->fresh(['entregaFest', 'proyecto']);
        $evento = $prospecto->entregaFest;

        // Solo procesamos si el estado es CONFORME
        if ($prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            return;
        }

        // 1. Mailable para el HTML del correo
        $mail = new ContratoPreliminarMail($prospecto);

        // 2. Resolver URL del PDF cargado en la pestaña Legal
        $urlPdf = $prospecto->getFirstMediaUrl('contrato-preliminar');

        // 3. Preparar el contacto (Titular) para n8n
        $titular = [
            'id' => $prospecto->id,
            'nombres' => $prospecto->nombres,
            'email' => $prospecto->email,
            'celular' => $prospecto->celular,
            'dni' => $prospecto->dni,
            'tipo' => 'Titular',
            'proyecto' => $prospecto->proyecto?->nombre,
            'pdf_url' => $urlPdf ?: null,
            'link_agendar' => $mail->link,
            'html' => $mail->render(),
        ];

        // 4. DISPARO A N8N: Envío de Contrato Preliminar
        $plantilla = $evento->plantillas()->where('tipo', 'contrato-preliminar')->first();
        $this->enviarContratoPreliminarAN8N($titular, $evento, $plantilla);

        // 5. EMITIMOS EVENTO HIJO: Invitación para agendar cita
        // Esto activará el listener EntregaFestCitaAgendarN8N
        EntregaFestCitaAgendar::dispatch($prospecto);
    }

    /**
     * Envía la notificación de contrato preliminar (PDF) a n8n con la clave 'titular'.
     */
    private function enviarContratoPreliminarAN8N($titular, $evento, $plantilla)
    {
        try {
            Http::post(config('services.n8n.entregafest.contrato_preliminar'), [
                'titular' => $titular,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '📄 Contrato Preliminar Aprobado: ' . $evento->nombre,
                    'subtitulo' => $plantilla?->subtitulo ?? 'Tu contrato preliminar ya está disponible para revisión.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $titular['pdf_url'] ?: $plantilla?->link_boton,
                ],
                'etapa' => 'contrato-preliminar'
            ]);

            Log::channel('entrega-fest')->info("[CONTRATO-PRELIMINAR-N8N] Enviado para Titular Prospecto #{$titular['id']}");

        } catch (\Exception $e) {
            Log::error("[CONTRATO-PRELIMINAR-N8N] Error: " . $e->getMessage());
        }
    }
}
