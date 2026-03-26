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

        // 2. Data del Titular (Propietario)
        $dataPropietario = null;
        if (!$prospecto->invitado) {
            $mailPropietario = new AsistenciaLinkMail($prospecto);
            $dataPropietario = [
                'id'      => $prospecto->id,
                'nombres' => $prospecto->nombres,
                'email'   => $prospecto->email,
                'celular' => $prospecto->celular,
                'dni'     => $prospecto->dni,
                'link'    => $mailPropietario->link,
                'html'    => $mailPropietario->render(),
                'tipo'    => 'Propietario',
            ];
        }

        // 3. Data de Copropietarios
        $dataCopropietarios = [];
        foreach ($prospecto->copropietarios as $cop) {
            if ($cop->invitado) continue;

            $mailCopro = new AsistenciaLinkCopropietarioMail($cop);
            $dataCopropietarios[] = [
                'id'      => $cop->id,
                'nombres' => $cop->nombres,
                'email'   => $cop->email,
                'celular' => $cop->celular,
                'dni'     => $cop->dni,
                'link'    => $mailCopro->link,
                'html'    => $mailCopro->render(),
                'tipo'    => 'Copropietario',
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
            Http::post(config('services.n8n.webhook_entrega_fest_confirmacion'), [
                'titular'        => $propietario,
                'copropietarios' => $copropietarios,
                'evento'         => $evento->nombre,
                'plantilla'      => [
                    'titulo'      => $plantilla?->titulo ?? '¡Confirmación Oficial!: ' . $evento->nombre,
                    'subtitulo'   => $plantilla?->subtitulo ?? 'Te invitamos a confirmar tu asistencia.',
                    'descripcion' => $plantilla?->descripcion ?? '',
                    'imagen_url'  => $plantilla?->getFirstMediaUrl('imagen') ?: $evento->getFirstMediaUrl('imagen_invitacion'),
                    'link_boton'  => $plantilla?->link_boton ?? '',
                ],
                'etapa' => 'confirmacion' // Etapa para historial
            ]);

            Log::channel('entrega-fest')->info("[INVITACION-PAQUETE-N8N] Enviada exitosamente a Prospecto #{$propietario['id']} con " . count($copropietarios) . " copropietarios.");

        } catch (\Exception $e) {
            Log::error("[INVITACION-PAQUETE-N8N] Error: " . $e->getMessage());
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
