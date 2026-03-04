<?php

namespace App\Listeners;

use App\Events\EntregaFestAsistenciaConfirmada;
use App\Models\InvitadoEntregaFest;
use App\Models\Cliente;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Mail\EntregaFest\InstruccionesEventoMail;
use App\Mail\EntregaFest\TicketAsistenciaMail;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionesAsistenciaConfirmada
{
    public function __construct(private WhatsappService $whatsapp)
    {
        //
    }

    public function handle(EntregaFestAsistenciaConfirmada $event): void
    {
        $invitado = $event->invitado;
        $invitado->load(['prospecto', 'copropietario', 'entregaFest']);

        $evento = $invitado->entregaFest;

        // Solo enviamos si confirmó asistencia (Estado en MAYÚSCULAS según migración)
        if ($invitado->estado_confirmacion !== InvitadoEntregaFest::ESTADO_CONFIRMADO) {
            return;
        }

        // 1. Ticket por correo
        if ($invitado->email) {
            try {
                Mail::to($invitado->email)->send(new TicketAsistenciaMail($invitado));
            } catch (\Exception $e) {
                Log::error("[NOTIFICACION-CONFIRMADA] Error enviando ticket a {$invitado->email}: " . $e->getMessage());
            }

            // 2. Instrucciones por correo
            try {
                Mail::to($invitado->email)->send(new InstruccionesEventoMail($invitado));
            } catch (\Exception $e) {
                Log::error("[NOTIFICACION-CONFIRMADA] Error enviando instrucciones a {$invitado->email}: " . $e->getMessage());
            }
        }

        // 3. WhatsApp con instrucciones e imagen
        if ($invitado->celular) {
            try {
                $celular = $this->formatearCelular($invitado->celular);

                $imagenUrl = 'https://plataforma-digital.aybarcorp.com/assets/imagen/construccion-aybar-corp.jpg';
                $caption = "Hola *{$invitado->nombre_completo}*, aquí te compartimos las instrucciones para el evento *{$evento->nombre}*. ¡Te esperamos!";

                $response = $this->whatsapp->sendImage($celular, $imagenUrl, $caption);

                if ($response) {
                    $this->registrarMensajeWsp($celular, $invitado->nombre_completo, $invitado->dni, $caption, $response['messages'][0]['id'] ?? 'AUTO_CONF_' . uniqid());
                }
            } catch (\Exception $e) {
                Log::error("[NOTIFICACION-CONFIRMADA] Error enviando WhatsApp a {$invitado->celular}: " . $e->getMessage());
            }
        }
    }

    private function formatearCelular(string $raw): string
    {
        $cel = preg_replace('/\D/', '', $raw);
        return strlen($cel) === 9 ? '51' . $cel : $cel;
    }

    private function registrarMensajeWsp(string $celular, string $nombre, string $dni, string $mensaje, string $waMessageId): void
    {
        $cliente = Cliente::where('dni', $dni)->first();
        $contacto = WhatsappContacto::updateOrCreate(
            ['wa_id' => $celular],
            ['nombre_wa' => $nombre, 'numero_celular' => $celular, 'cliente_id' => $cliente?->id]
        );
        $conversacion = WhatsappConversacion::firstOrCreate(
            ['contacto_id' => $contacto->id],
            ['cliente_id' => $cliente?->id, 'estado' => 'asignado', 'departamento_destino' => 'backoffice', 'agente_id' => auth()->id()]
        );
        $conversacion->update(['last_message_at' => now()]);
        WhatsappMensaje::create([
            'conversacion_id' => $conversacion->id,
            'direccion' => 'saliente',
            'tipo' => 'imagen',
            'contenido' => $mensaje,
            'wa_message_id' => $waMessageId,
            'estado' => 'enviado',
        ]);
    }
}
