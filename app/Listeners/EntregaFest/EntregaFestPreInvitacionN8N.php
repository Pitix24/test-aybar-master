<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestPreInvitacion;
use App\Models\CopropietarioEntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Mail\EntregaFest\PreInvitacionPropietarioMail;
use App\Mail\EntregaFest\PreInvitacionCopropietarioMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EntregaFestPreInvitacionN8N
{
    /**
     * Handle the event.
     */
    public function handle(EntregaFestPreInvitacion $event): void
    {
        $evento = $event->evento;

        // 1. Buscamos la plantilla configurada para este evento
        $plantilla = $evento->plantillas()->where('tipo', 'pre-invitacion')->first();

        $etapa = $plantilla->tipo ?? 'pre-invitacion';
        $contactos = ProspectoEntregaFest::where('entrega_fest_id', $evento->id)
            ->whereNotIn('estado_cliente', ['PLANTON', 'DESISTIMIENTO', 'DEVOLUCION_DE_APORTES', 'CARTA_NOTARIAL', 'RESOLUCION_DE_CONTRATO'])
            ->where(function ($query) use ($etapa) {
                // Traelo si le falta el Email EXITOSO...
                $query->whereDoesntHave('historialComunicaciones', function ($q) use ($etapa) {
                    $q->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado');
                })
                    // ...O si le falta el WhatsApp EXITOSO
                    ->orWhereDoesntHave('historialComunicaciones', function ($q) use ($etapa) {
                    $q->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado');
                });
            })
            ->with(['copropietarios', 'entregaFest'])
            ->get()
            ->map(function (ProspectoEntregaFest $prospecto) use ($plantilla, $etapa) {

                // Cálculo individual por canal para n8n
                $yaFueEmail = $prospecto->historialComunicaciones()
                    ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->exists();

                $yaFueWhatsapp = $prospecto->historialComunicaciones()
                    ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->exists();

                $mailPropietario = new PreInvitacionPropietarioMail($prospecto, $plantilla);

                return [
                    'id' => $prospecto->id,
                    'email' => $prospecto->email,
                    'nombres' => $prospecto->nombres,
                    'celular' => $prospecto->celular,
                    'dni' => $prospecto->dni,
                    'link' => $mailPropietario->link,
                    'html' => $mailPropietario->render(),
                    'enviar_email' => !$yaFueEmail,      // Etiqueta para n8n
                    'enviar_whatsapp' => !$yaFueWhatsapp, // Etiqueta para n8n
                    'tipo' => 'Propietario',

                    'copropietarios' => $prospecto->copropietarios->map(function ($copro) use ($plantilla) {
                        $mailCopro = new PreInvitacionCopropietarioMail($copro, $plantilla);
                        return [
                            'id' => $copro->id,
                            'nombres' => $copro->nombres,
                            'email' => $copro->email,
                            'celular' => $copro->celular,
                            'dni' => $copro->dni,
                            'link' => $mailCopro->link,
                            'html' => $mailCopro->render(),
                        ];
                    })
                ];
            })
            ->toArray();

        if (empty($contactos)) {
            Log::channel('entrega-fest')->warning("[PRE-INVITACION-N8N] No hay contactos con email para el evento #{$evento->id}");
            return;
        }

        // 2. Enviamos todo el paquete a n8n incluyendo la data de la PLANTILLA
        try {
            Http::post(config('services.n8n.entregafest.pre_invitacion'), [
                'contactos' => $contactos,
                'evento' => $evento->nombre,
                'plantilla' => [
                    'titulo' => $plantilla?->titulo ?? 'Pre-invitación: ' . $evento->nombre,
                    'subtitulo' => $plantilla?->subtitulo ?? '',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url' => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton' => $plantilla?->link_boton ?? '',
                ],
                'etapa' => 'pre-invitacion'
            ]);

            Log::channel('entrega-fest')->info("[PRE-INVITACION-N8N] Enviada exitosamente para " . count($contactos) . " prospectos del evento #{$evento->id}");
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error("[PRE-INVITACION-N8N] Error enviando a n8n: " . $e->getMessage());
        }
    }
}
