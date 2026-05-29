<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestPreInvitacion;
use App\Models\ProspectoEntregaFest;
use App\Mail\EntregaFest\PreInvitacionPropietarioMail;
use App\Mail\EntregaFest\PreInvitacionCopropietarioMail;
use App\Support\EntregaFestCelular;
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
            ->whereHas('estadoCliente', function ($query) {
                $query->whereNotIn('nombre', ['PLANTON', 'DESISTIMIENTO', 'DEVOLUCION DE APORTES', 'CARTA NOTARIAL', 'RESOLUCION DE CONTRATO']);
            })
            ->where(function ($query) use ($etapa) {
                // El titular necesita algo y NO ha respondido
                $query->where(function ($q) use ($etapa) {
                    $q->whereNull('preinvitacion_confirmada')
                        ->where(function ($qq) use ($etapa) {
                            $qq->whereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                                $h->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado');
                            })
                                ->orWhereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                                    $h->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado');
                                });
                        });
                })
                    // ...o algún copropietario necesita algo y NO ha respondido
                    ->orWhereHas('copropietarios', function ($cq) use ($etapa) {
                        $cq->whereNull('preinvitacion_confirmada')
                            ->where(function ($qq) use ($etapa) {
                                $qq->whereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                                    $h->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado');
                                })
                                    ->orWhereDoesntHave('historialComunicaciones', function ($h) use ($etapa) {
                                        $h->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado');
                                    });
                            });
                    });
            })
            ->with(['copropietarios.historialComunicaciones', 'entregaFest', 'historialComunicaciones'])
            ->get()
            ->unique('dni') // <--- IMPORTANTE: Solo un envío por DNI de Titular
            ->values()
            ->map(function (ProspectoEntregaFest $prospecto) use ($plantilla, $etapa) {

                // Cálculo individual por canal para n8n (Titular)
                // Solo enviamos si no ha confirmado/rechazado Y no tiene envíos previos
                $yaFueEmail = $prospecto->historialComunicaciones
                    ->where('etapa', $etapa)->where('canal', 'email')->where('estado', 'enviado')->isNotEmpty();

                $yaFueWhatsapp = $prospecto->historialComunicaciones
                    ->where('etapa', $etapa)->where('canal', 'whatsapp')->where('estado', 'enviado')->isNotEmpty();

                $mailPropietario = new PreInvitacionPropietarioMail($prospecto, $plantilla);

                return [
                    'id' => $prospecto->id,
                    'nombres' => $prospecto->nombres,
                    'email' => $prospecto->email,
                    'celular' => EntregaFestCelular::peru($prospecto->celular),
                    'dni' => $prospecto->dni,
                    'link' => $mailPropietario->link,
                    'html' => $mailPropietario->render(),
                    'enviar_email' => is_null($prospecto->preinvitacion_confirmada) && !$yaFueEmail,
                    'enviar_whatsapp' => is_null($prospecto->preinvitacion_confirmada) && !$yaFueWhatsapp,
                    'tipo' => 'Propietario',

                    'copropietarios' => $prospecto->copropietarios
                        ->whereNull('preinvitacion_confirmada')
                        ->unique('dni') // <--- IMPORTANTE: Solo un envío por DNI de Copropietario
                        ->map(function ($copro) use ($plantilla, $etapa) {

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
                                'celular' => EntregaFestCelular::peru($copro->celular),
                                'dni' => $copro->dni,
                                'link' => $mailCopro->link,
                                'html' => $mailCopro->render(),
                                'enviar_email' => !$yaFueEmailCop,
                                'enviar_whatsapp' => !$yaFueWhatsappCop,
                                'tipo' => 'Copropietario',
                            ];
                        })->values()
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
