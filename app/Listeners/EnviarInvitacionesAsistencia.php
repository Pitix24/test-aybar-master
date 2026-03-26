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
use Illuminate\Support\Facades\Http;

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

        // 1. Buscamos la plantilla oficial de "confirmacion"
        $plantilla = $evento->plantillas()->where('tipo', 'confirmacion')->first();

        // 2. DISPARO PARA EL TITULAR (PROPIETARIO)
        if (!$prospecto->invitado) {
            $mailPropietario = new AsistenciaLinkMail($prospecto);
            $this->enviarAN8N($prospecto, $evento, $plantilla, $mailPropietario->render(), 'Propietario', route('public.entrega-fest.asistencia', [$evento->slug, $prospecto->id]));
        }

        // 3. DISPARO PARA CADA COPROPIETARIO
        foreach ($prospecto->copropietarios as $cop) {
            if ($cop->invitado) continue;
            
            $mailCopro = new \App\Mail\EntregaFest\AsistenciaLinkCopropietarioMail($cop);
            $this->enviarAN8N($cop, $evento, $plantilla, $mailCopro->render(), 'Copropietario', route('public.entrega-fest.asistencia.copropietario', [$evento->slug, $cop->id]));
        }
    }

    private function enviarAN8N($persona, $evento, $plantilla, $html, $tipoPersona, $link)
    {
        try {
            Http::post(config('services.n8n.webhook_entrega_fest_confirmacion'), [
                'contacto' => [
                    'id'      => $persona->id,
                    'email'   => $persona->email,
                    'nombres' => $persona->nombres,
                    'celular' => $persona->celular,
                    'dni'     => $persona->dni,
                    'link'    => $link,
                    'html'    => $html,
                    'tipo'    => $tipoPersona,
                ],
                'evento'   => $evento->nombre,
                'plantilla' => [
                    'titulo'      => $plantilla?->titulo ?? '¡Confirmación Oficial!: ' . $evento->nombre,
                    'subtitulo'   => $plantilla?->subtitulo ?? 'Te invitamos a confirmar tu asistencia.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url'  => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton'  => $plantilla?->link_boton ?? '',
                ],
                'etapa' => 'invitacion' // Muy importante para tu API de historial
            ]);

            Log::channel('entrega-fest')->info("[INVITACION-INDIVIDUAL-N8N] Enviada a {$tipoPersona} #{$persona->id}");

        } catch (\Exception $e) {
            Log::error("[INVITACION-INDIVIDUAL-N8N] Error: " . $e->getMessage());
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
            'tipo' => 'texto',
            'contenido' => $mensaje,
            'wa_message_id' => $waMessageId,
            'estado' => 'enviado',
        ]);
    }
}
