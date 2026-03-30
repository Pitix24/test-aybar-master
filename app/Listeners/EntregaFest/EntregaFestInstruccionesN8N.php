<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestInstrucciones;
use App\Mail\EntregaFest\InstruccionesEventoMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestInstruccionesN8N
{
    /**
     * Handle the event.
     */
    public function handle(EntregaFestInstrucciones $event): void
    {
        $invitado = $event->invitado->load(['entregaFest']);
        $evento = $invitado->entregaFest;

        // Solo procesamos si el invitado confirmó su asistencia
        if (!$invitado->confirmado) {
            return;
        }

        // 1. Generamos el HTML de las Instrucciones
        $mail = new InstruccionesEventoMail($invitado);

        // 2. Preparamos el contacto base
        $contacto = [
            'id' => $invitado->id,
            'nombres' => $invitado->nombre_completo,
            'email' => $invitado->email,
            'celular' => $invitado->celular,
            'dni' => $invitado->dni,
            'tipo' => $invitado->tipo,
            'html' => $mail->render(),
        ];

        // 3. DISPARO: Instrucciones del Evento a N8N
        $plantillaInst = $evento->plantillas()->where('tipo', 'instrucciones')->first();
        $this->enviarInstruccionesAN8N($contacto, $evento, $plantillaInst);
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
