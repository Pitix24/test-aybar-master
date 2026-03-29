<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaAgendar;
use App\Mail\EntregaFest\CitaAgendarMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestCitaAgendarN8N
{
    public function handle(EntregaFestCitaAgendar $event): void
    {
        $prospecto = $event->prospecto->fresh(['entregaFest']);
        $evento = $prospecto->entregaFest;

        // Solo si aún no tiene fecha de firma agendada
        if ($prospecto->fecha_firma) {
            return;
        }

        // 1. Buscamos la plantilla oficial de "cita-agendar"
        $plantilla = $evento->plantillas()->where('tipo', 'cita-agendar')->first();

        // 2. Data del Titular (Propietario)
        $mail = new CitaAgendarMail($prospecto);
        $dataPropietario = [
            'id' => $prospecto->id,
            'nombres' => $prospecto->nombres,
            'email' => $prospecto->email,
            'celular' => $prospecto->celular,
            'dni' => $prospecto->dni,
            'link' => $mail->link,
            'html' => $mail->render(),
            'tipo' => 'Propietario',
        ];

        // 3. ENVIAMOS UN SOLO PAQUETE A N8N (Sin copropietarios)
        $this->enviarPaqueteAN8N($dataPropietario, $evento, $plantilla);
    }

    private function enviarPaqueteAN8N($propietario, $evento, $plantilla)
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
                'etapa' => 'cita_agendar' // Etapa para historial
            ]);

            Log::channel('entrega-fest')->info("[CITA-AGENDAR-PAQUETE-N8N] Enviada exitosamente a Prospecto #{$propietario['id']}");

        } catch (\Exception $e) {
            Log::error("[CITA-AGENDAR-PAQUETE-N8N] Error: " . $e->getMessage());
        }
    }
}
