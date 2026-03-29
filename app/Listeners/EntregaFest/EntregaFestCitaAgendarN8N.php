<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaAgendar;
use App\Mail\EntregaFest\CitaAgendarMail;
use App\Mail\EntregaFest\ContratoPreliminarMail;
use App\Models\Cliente;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EntregaFestCitaAgendarN8N
{
    public function __construct(private WhatsappService $whatsapp)
    {
        //
    }

    public function handle(EntregaFestCitaAgendar $event): void
    {
        $prospecto = $event->prospecto->fresh(['entregaFest']);
        $evento = $prospecto->entregaFest;

        // Solo si aún no tiene fecha de firma agendada
        if ($prospecto->fecha_firma) {
            return;
        }

        $enviadoEmail = false;
        $enviadoWsp = false;

        // ── Email ────────────────────────────────────────────────────────
        if ($prospecto->email) {
            try {
                Mail::to($prospecto->email)->send(new CitaAgendarMail($prospecto));
                $enviadoEmail = true;
            } catch (\Exception $e) {
                Log::error('[CITA AGENDAR] Correo a ' . $prospecto->email . ': ' . $e->getMessage());
            }
        }

        // ── WhatsApp ─────────────────────────────────────────────────────
        if ($prospecto->celular) {
            $celular = $this->formatearCelular($prospecto->celular);
            $link = route('public.entrega-fest.firma', [$evento->slug, $prospecto->id]);
            $mensaje = "Hola *{$prospecto->nombres}*, tu contrato preliminar para el evento *{$evento->nombre}* está aprobado 🎉. Agenda aquí tu cita de firma: $link";

            $response = $this->whatsapp->sendText($celular, $mensaje);
            if ($response) {
                $enviadoWsp = true;
                $this->registrarMensajeWsp($celular, $prospecto->nombres, $prospecto->dni, $mensaje, $response['messages'][0]['id'] ?? 'AUTO_FIRMA_' . uniqid());
            }
        }

        Log::channel('entrega-fest')->info("[CONTRATO PRELIMINAR] Prospecto {$prospecto->id}: email={$enviadoEmail}, wsp={$enviadoWsp}.");
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
