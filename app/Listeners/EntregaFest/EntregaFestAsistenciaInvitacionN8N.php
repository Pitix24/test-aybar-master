<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestAsistenciaInvitacion;
use App\Mail\EntregaFest\AsistenciaInvitacionCopropietarioMail;
use App\Mail\EntregaFest\AsistenciaInvitacionPropietarioMail;
use App\Support\EntregaFestCelular;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Support\VerificaEventoVigente;

class EntregaFestAsistenciaInvitacionN8N
{
    use VerificaEventoVigente; // Importamos el trait para verificar si el evento sigue vigente antes de enviar a n8n
    /**
     * Handle the event.
     */
    public function handle(EntregaFestAsistenciaInvitacion $event): void
    {
        $prospecto = $event->prospecto->fresh(['invitado', 'copropietarios.invitado', 'entregaFest', 'historialComunicaciones', 'copropietarios.historialComunicaciones']);
        $evento = $prospecto->entregaFest;

        // 🛑 FILTRO: Si el evento ya pasó, NO enviamos a n8n
        if (!$this->eventoVigente($evento, 'ASISTENCIA-INVITACION-MASIVO-N8N')) {
            return;
        }
        // 🛑 FILTRO: Si el prospecto tiene observación legal, cancelamos el envío
        if ($prospecto->observacion_legal) {
            Log::channel('entrega-fest')->warning("[ASISTENCIA-INVITACION-N8N] Envío abortado: Prospecto #{$prospecto->id} tiene restricción/observación legal.");
            return;
        }

        // 1. Buscamos la plantilla oficial de "confirmacion"
        $plantilla = $evento->plantillas()->where('tipo', 'asistencia-invitacion')->first();
        $etapa = $plantilla->tipo ?? 'asistencia-invitacion';

        // 2. Data del Titular (Propietario)
        $dataPropietario = null;
        if (!$prospecto->invitado) {
            $yaFueEmail = $prospecto->historialComunicaciones
                ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

            $yaFueWhatsapp = $prospecto->historialComunicaciones
                ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

            $mailPropietario = new AsistenciaInvitacionPropietarioMail($prospecto);
            $dataPropietario = [
                'id' => $prospecto->id,
                'nombres' => $prospecto->nombres,
                'email' => $prospecto->email,
                'celular' => EntregaFestCelular::peru($prospecto->celular),
                'dni' => $prospecto->dni,
                'link' => $mailPropietario->link,
                'html' => $mailPropietario->render(),
                'enviar_email' => !$yaFueEmail,
                'enviar_whatsapp' => !$yaFueWhatsapp,
                'tipo' => 'Propietario',
            ];
        }

        // 3. Data de Copropietarios
        $dataCopropietarios = [];
        foreach ($prospecto->copropietarios as $cop) {
            if ($cop->invitado)
                continue;

            $yaFueEmailCop = $cop->historialComunicaciones
                ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

            $yaFueWhatsappCop = $cop->historialComunicaciones
                ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

            $mailCopro = new AsistenciaInvitacionCopropietarioMail($cop);
            $dataCopropietarios[] = [
                'id' => $cop->id,
                'nombres' => $cop->nombres,
                'email' => $cop->email,
                'celular' => EntregaFestCelular::peru($cop->celular),
                'dni' => $cop->dni,
                'link' => $mailCopro->link,
                'html' => $mailCopro->render(),
                'enviar_email' => !$yaFueEmailCop,
                'enviar_whatsapp' => !$yaFueWhatsappCop,
                'tipo' => 'Copropietario',
            ];
        }

        // 4. Si no hay nadie por invitar, nos retiramos
        if (!$dataPropietario && empty($dataCopropietarios)) {
            return;
        }

        // 5. ENVIAMOS UN SOLO PAQUETE A N8N
        $this->enviarPaqueteAN8N($dataPropietario, $dataCopropietarios, $evento, $plantilla, $etapa);
    }

    private function enviarPaqueteAN8N($propietario, $copropietarios, $evento, $plantilla, $etapa)
    {
        try {
            Http::post(config('services.n8n.entregafest.asistencia_invitacion'), [
                'titular' => $propietario,
                'copropietarios' => $copropietarios,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? '¡Confirmación Oficial!: ' . $evento->nombre,
                    'subtitulo' => $plantilla?->subtitulo ?? 'Te invitamos a confirmar tu asistencia.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $plantilla?->link_boton ?? '',
                ],
                'etapa' => $etapa // Etapa dinámica para historial
            ]);

            $idPropietario = $propietario['id'] ?? 'N/A';
            Log::channel('entrega-fest')->info("[INVITACION-PAQUETE-N8N] Enviada exitosamente a Prospecto #{$idPropietario} con " . count($copropietarios) . " copropietarios.");
        } catch (\Exception $e) {
            Log::error("[INVITACION-PAQUETE-N8N] Error: " . $e->getMessage());
        }
    }
}
