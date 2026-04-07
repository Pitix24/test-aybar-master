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
        $invitado = $event->invitado->load(['prospecto.historialComunicaciones', 'copropietario.historialComunicaciones', 'entregaFest']);
        $evento = $invitado->entregaFest;

        // Definimos la persona (Titular o Copropietario)
        $persona = $invitado->prospecto ?? $invitado->copropietario;

        // Solo procesamos si la persona confirmó su asistencia (invitacion_confirmada = true)
        if (!$persona?->invitacion_confirmada) {
            Log::channel('entrega-fest')->info("[INSTRUCCIONES] Saltado para {$invitado->nombre_completo} (No confirmó)");
            return;
        }

        // 1. Verificamos si ya se enviaron comunicaciones para esta etapa
        $etapa = 'instrucciones';
        $plantillaInst = $evento->plantillas()->where('tipo', $etapa)->first();
        if ($plantillaInst) {
            $etapa = $plantillaInst->tipo;
        }

        $yaFueEmail = $persona->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

        $yaFueWhatsapp = $persona->historialComunicaciones
            ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

        // 2. Generamos el HTML de las Instrucciones
        $mail = new InstruccionesEventoMail($invitado);

        // 3. Preparamos el contacto base (ID de Persona)
        $contacto = [
            'id' => $invitado->prospecto_entrega_fest_id ?? $invitado->copropietario_entrega_fest_id,
            'nombres' => $invitado->nombre_completo,
            'email' => $invitado->email,
            'celular' => $invitado->celular,
            'dni' => $invitado->dni,
            'tipo' => $invitado->prospecto_entrega_fest_id ? 'Propietario' : 'Copropietario',
            'html' => $mail->render(),
            'enviar_email' => !$yaFueEmail,
            'enviar_whatsapp' => !$yaFueWhatsapp,
        ];

        // 4. DISPARO: Instrucciones del Evento a N8N
        $this->enviarInstruccionesAN8N($contacto, $evento, $plantillaInst, $etapa);
    }

    /**
     * Envía las instrucciones del evento a n8n.
     */
    private function enviarInstruccionesAN8N($contacto, $evento, $plantilla, $etapa)
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
                'etapa' => $etapa
            ]);

            Log::channel('entrega-fest')->info("[INSTRUCCIONES-REGISTRO-N8N] Enviadas para {$contacto['tipo']} #{$contacto['id']}");
        } catch (\Exception $e) {
            Log::error("[INSTRUCCIONES-REGISTRO-N8N] Error: " . $e->getMessage());
        }
    }
}
