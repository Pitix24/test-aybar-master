<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaAgendar;
use App\Mail\EntregaFest\CitaAgendarMail;
use App\Support\EntregaFestCelular;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestCitaAgendarN8N
{
    public function handle(EntregaFestCitaAgendar $event): void
    {
        $prospecto = $event->prospecto->load(['entregaFest', 'historialComunicaciones']);
        $evento = $prospecto->entregaFest;

        // Solo si aún no tiene fecha de firma agendada
        if ($prospecto->fecha_firma) {
            Log::channel('entrega-fest')->info("[CITA-AGENDAR] Saltado para #{$prospecto->id} (Ya tiene fecha de firma)");
            return;
        }

        // 🛑 FILTRO: Si el prospecto tiene observación legal, cancelamos el envío
        if ($prospecto->observacion_legal) {
            Log::channel('entrega-fest')->warning("[CITA-AGENDAR] Envío abortado: Prospecto #{$prospecto->id} tiene restricción/observación legal.");
            return;
        }

        // 1. Verificamos si ya se enviaron comunicaciones para esta etapa
        $etapa = 'cita-agendar';
        $plantilla = $evento->plantillas()->where('tipo', $etapa)->first();
        if ($plantilla) {
            $etapa = $plantilla->tipo;
        }

        $yaFueEmail = $prospecto->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

        $yaFueWhatsapp = $prospecto->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

        // 2. Data del Propietario
        $mail = new CitaAgendarMail($prospecto);
        $dataPropietario = [
            'id' => $prospecto->id,
            'nombres' => $prospecto->nombres,
            'email' => $prospecto->email,
            'celular' => EntregaFestCelular::peru($prospecto->celular),
            'dni' => $prospecto->dni,
            'link' => $mail->link,
            'html' => $mail->render(),
            'tipo' => 'Propietario',
            'enviar_email' => !$yaFueEmail,
            'enviar_whatsapp' => !$yaFueWhatsapp,
        ];

        // 3. ENVIAMOS UN SOLO PAQUETE A N8N (Sin copropietarios)
        $this->enviarPaqueteAN8N($dataPropietario, $evento, $plantilla, $etapa);
    }

    private function enviarPaqueteAN8N($propietario, $evento, $plantilla, $etapa)
    {
        try {
            Http::post(config('services.n8n.entregafest.cita_agendar'), [
                'titular' => $propietario,
                'copropietarios' => [], // Solicitado: no deben entrar los copropietarios
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '📅 Contrato Preliminar: ' . $evento->nombre,
                    'subtitulo' => $plantilla?->subtitulo ?? 'Agenda tu cita de firma.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $plantilla?->link_boton ?? '',
                ],
                'etapa' => $etapa
            ]);

            Log::channel('entrega-fest')->info("[CITA-AGENDAR-PAQUETE-N8N] Enviada exitosamente a Prospecto #{$propietario['id']}");
        } catch (\Exception $e) {
            Log::error("[CITA-AGENDAR-PAQUETE-N8N] Error: " . $e->getMessage());
        }
    }
}
