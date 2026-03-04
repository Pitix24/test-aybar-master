<?php

namespace App\Listeners;

use App\Events\ProspectoBackofficeConforme;
use App\Mail\EntregaFest\AsistenciaLinkMail;
use App\Mail\EntregaFest\AsistenciaLinkCopropietarioMail;
use App\Models\Cliente;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarInvitacionesAsistencia
{
    public function __construct(private WhatsappService $whatsapp)
    {
        //
    }

    public function handle(ProspectoBackofficeConforme $event): void
    {
        $prospecto = $event->prospecto->fresh(['invitado', 'copropietarios.invitado', 'entregaFest']);
        $evento = $prospecto->entregaFest;

        $enviadosEmail = 0;
        $enviadosWsp = 0;

        // ── 1. TITULAR ──────────────────────────────────────────────────
        if (!$prospecto->invitado) {
            if ($prospecto->email) {
                try {
                    Mail::to($prospecto->email)->send(new AsistenciaLinkMail($prospecto));
                    $enviadosEmail++;
                } catch (\Exception $e) {
                    Log::error("[INVITACION-ASISTENCIA] Correo titular {$prospecto->email}: " . $e->getMessage());
                }
            }

            if ($prospecto->celular) {
                $celular = $this->formatearCelular($prospecto->celular);
                $link = route('public.entrega-fest.asistencia', [$evento->slug, $prospecto->id]);
                $mensaje = "Hola *{$prospecto->nombres}*, ya tenemos tu evaluación lista para el evento *{$evento->nombre}*. Confirma tu asistencia aquí: $link";

                $response = $this->whatsapp->sendText($celular, $mensaje);
                if ($response) {
                    $enviadosWsp++;
                    $this->registrarMensajeWsp($celular, $prospecto->nombres, $prospecto->dni, $mensaje, $response['messages'][0]['id'] ?? 'AUTO_' . uniqid());
                }
            }
        }

        // ── 2. COPROPIETARIOS ────────────────────────────────────────────
        foreach ($prospecto->copropietarios as $cop) {
            if ($cop->invitado) {
                continue;
            }

            if ($cop->email) {
                try {
                    Mail::to($cop->email)->send(new AsistenciaLinkCopropietarioMail($cop));
                    $enviadosEmail++;
                } catch (\Exception $e) {
                    Log::error("[INVITACION-ASISTENCIA] Correo copropietario {$cop->email}: " . $e->getMessage());
                }
            }

            if ($cop->celular) {
                $celular = $this->formatearCelular($cop->celular);
                $link = route('public.entrega-fest.asistencia.copropietario', [$evento->slug, $cop->id]);
                $mensaje = "Hola *{$cop->nombres}*, ya tienes evaluación lista para el evento *{$evento->nombre}*. Confirma tu asistencia aquí: $link";

                $response = $this->whatsapp->sendText($celular, $mensaje);
                if ($response) {
                    $enviadosWsp++;
                    $this->registrarMensajeWsp($celular, $cop->nombres, $cop->dni, $mensaje, $response['messages'][0]['id'] ?? 'AUTO_COP_' . uniqid());
                }
            }
        }

        Log::channel('entrega-fest')->info("[INVITACION-ASISTENCIA] Prospecto {$prospecto->id}: {$enviadosEmail} correos, {$enviadosWsp} WhatsApp enviados.");
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
            'tipo' => 'texto',
            'contenido' => $mensaje,
            'wa_message_id' => $waMessageId,
            'estado' => 'enviado',
        ]);
    }
}
