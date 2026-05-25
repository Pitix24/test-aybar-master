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
        $prospecto = $event->prospecto->load(['entregaFest', 'proyecto', 'historialComunicaciones']);
        $evento = $prospecto->entregaFest;

        // Solo procesamos si el estado es CONFORME
        if ($prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            Log::channel('entrega-fest')->info("[CONTRATO-PRELIMINAR] Saltado para #{$prospecto->id} (Estado no es CONFORME)");
            return;
        }

        // 1. Verificamos si ya se enviaron comunicaciones para esta etapa
        $etapa = 'contrato-preliminar';
        $plantilla = $evento->plantillas()->where('tipo', $etapa)->first();
        if ($plantilla) {
            $etapa = $plantilla->tipo;
        }

        $yaFueEmail = $prospecto->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

        $yaFueWhatsapp = $prospecto->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

        // 2. Mailable para el HTML del correo
        $mail = new ContratoPreliminarMail($prospecto);

        // 3. Resolver URL del PDF cargado en la pestaña Legal
        $urlPdf = $prospecto->getFirstMediaUrl('contrato-preliminar');

        // 4. Preparar el contacto (Propietario) para n8n
        $titular = [
            'id' => $prospecto->id,
            'nombres' => $prospecto->nombres,
            'email' => $prospecto->email,
            'celular' => $prospecto->celular,
            'dni' => $prospecto->dni,
            'tipo' => 'Propietario',
            'proyecto' => $prospecto->proyecto?->nombre,
            'pdf_url' => $urlPdf ?: null,
            'link_agendar' => $mail->link,
            'html' => $mail->render(),
            'enviar_email' => !$yaFueEmail,
            'enviar_whatsapp' => !$yaFueWhatsapp,
        ];

        // 4. DISPARO A N8N: Envío de Contrato Preliminar
        $this->enviarContratoPreliminarAN8N($titular, $evento, $plantilla, $etapa);

        // 5. EMITIMOS EVENTO HIJO: Invitación para agendar cita
        EntregaFestCitaAgendar::dispatch($prospecto);
    }

    /**
     * Envía la notificación de contrato preliminar (PDF) a n8n con la clave 'titular'.
     */
    private function enviarContratoPreliminarAN8N($titular, $evento, $plantilla, $etapa)
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
                    'link_boton' => $titular['link_agendar'] ?: ($titular['pdf_url'] ?: $plantilla?->link_boton),
                ],
                'etapa' => $etapa
            ]);

            Log::channel('entrega-fest')->info("[CONTRATO-PRELIMINAR-N8N] Enviado para Propietario Prospecto #{$titular['id']}");
        } catch (\Exception $e) {
            Log::error("[CONTRATO-PRELIMINAR-N8N] Error: " . $e->getMessage());
        }
    }
}
