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
                // El titular necesita algo...
                $query->where(function ($q) use ($etapa) {
                    $q->whereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                        $h->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado');
                    })
                        ->orWhereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                            $h->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado');
                        });
                })
                    // ...o algún copropietario necesita algo
                    ->orWhereHas('copropietarios', function ($cq) use ($etapa) {
                    $cq->whereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                        $h->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado');
                    })
                        ->orWhereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                            $h->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado');
                        });
                });
            })
            ->with(['copropietarios.historialComunicaciones', 'entregaFest', 'historialComunicaciones'])
            ->get()
            ->map(function (ProspectoEntregaFest $prospecto) use ($plantilla, $etapa) {

                // Cálculo individual por canal para n8n (Titular)
                $yaFueEmail = $prospecto->historialComunicaciones
                    ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

                $yaFueWhatsapp = $prospecto->historialComunicaciones
                    ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

                $mailPropietario = new PreInvitacionPropietarioMail($prospecto, $plantilla);

                return [
                    'id' => $prospecto->id,
                    'nombres' => $prospecto->nombres,
                    'email' => $prospecto->email,
                    'celular' => $prospecto->celular,
                    'dni' => $prospecto->dni,
                    'link' => $mailPropietario->link,
                    'html' => $mailPropietario->render(),
                    'enviar_email' => !$yaFueEmail,      // Etiqueta para n8n
                    'enviar_whatsapp' => !$yaFueWhatsapp, // Etiqueta para n8n
                    'tipo' => 'Propietario',

                    'copropietarios' => $prospecto->copropietarios->map(function ($copro) use ($plantilla, $etapa) {

                        // Cálculo individual por canal para n8n (Copropietario)
                        $yaFueEmailCop = $copro->historialComunicaciones
                            ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

                        $yaFueWhatsappCop = $copro->historialComunicaciones
                            ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

                        $mailCopro = new PreInvitacionCopropietarioMail($copro, $plantilla);

                        return [
                            'id' => $copro->id,
                            'nombres' => $copro->nombres,
                            'email' => $copro->email,
                            'celular' => $copro->celular,
                            'dni' => $copro->dni,
                            'link' => $mailCopro->link,
                            'html' => $mailCopro->render(),
                            'enviar_email' => !$yaFueEmailCop,
                            'enviar_whatsapp' => !$yaFueWhatsappCop,
                            'tipo' => 'Copropietario',
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
