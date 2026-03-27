<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestAsistenciaInvitacion;
use App\Mail\EntregaFest\AsistenciaInvitacionCopropietarioMail;
use App\Mail\EntregaFest\AsistenciaInvitacionPropietarioMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EntregaFestInvitacionAsistenciaN8N
{
    public function handle(EntregaFestAsistenciaInvitacion $event): void
    {
        $prospecto = $event->prospecto->fresh(['invitado', 'copropietarios.invitado', 'entregaFest']);
        $evento = $prospecto->entregaFest;

        // 1. Buscamos la plantilla oficial de "confirmacion"
        $plantilla = $evento->plantillas()->where('tipo', 'asistencia-invitacion')->first();

        // 2. Data del Titular (Propietario)
        $dataPropietario = null;
        if (!$prospecto->invitado) {
            $mailPropietario = new AsistenciaInvitacionPropietarioMail($prospecto);
            $dataPropietario = [
                'id' => $prospecto->id,
                'nombres' => $prospecto->nombres,
                'email' => $prospecto->email,
                'celular' => $prospecto->celular,
                'dni' => $prospecto->dni,
                'link' => $mailPropietario->link,
                'html' => $mailPropietario->render(),
                'tipo' => 'Propietario',
            ];
        }

        // 3. Data de Copropietarios
        $dataCopropietarios = [];
        foreach ($prospecto->copropietarios as $cop) {
            if ($cop->invitado)
                continue;

            $mailCopro = new AsistenciaInvitacionCopropietarioMail($cop);
            $dataCopropietarios[] = [
                'id' => $cop->id,
                'nombres' => $cop->nombres,
                'email' => $cop->email,
                'celular' => $cop->celular,
                'dni' => $cop->dni,
                'link' => $mailCopro->link,
                'html' => $mailCopro->render(),
                'tipo' => 'Copropietario',
            ];
        }

        // 4. Si no hay nadie por invitar, nos retiramos
        if (!$dataPropietario && empty($dataCopropietarios)) {
            return;
        }

        // 5. ENVIAMOS UN SOLO PAQUETE A N8N
        $this->enviarPaqueteAN8N($dataPropietario, $dataCopropietarios, $evento, $plantilla);
    }

    private function enviarPaqueteAN8N($propietario, $copropietarios, $evento, $plantilla)
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
                'etapa' => 'confirmacion' // Etapa para historial
            ]);

            Log::channel('entrega-fest')->info("[INVITACION-PAQUETE-N8N] Enviada exitosamente a Prospecto #{$propietario['id']} con " . count($copropietarios) . " copropietarios.");

        } catch (\Exception $e) {
            Log::error("[INVITACION-PAQUETE-N8N] Error: " . $e->getMessage());
        }
    }
}
