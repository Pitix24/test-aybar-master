<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\WhatsappContacto;
use App\Models\Cliente;
use App\Services\WhatsappService;

class ProcessIncomingWhatsapp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (isset($this->payload['entry'][0]['changes'][0]['value'])) {
                $value = $this->payload['entry'][0]['changes'][0]['value'];

                // 1. Manejar Mensajes Entrantes
                if (isset($value['messages'][0])) {
                    Log::channel('whatsapp')->info('--- JOB: Procesando Mensaje ---');
                    $this->processIncomingMessage($value['messages'][0], $this->payload);
                }

                // 2. Manejar Estados (Enviado, Entregado, Leído)
                if (isset($value['statuses'][0])) {
                    Log::channel('whatsapp')->info('--- JOB: Procesando Estado ---');
                    $this->updateMessageStatus($value['statuses'][0]);
                }
            }
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Error en Job ProcessIncomingWhatsapp: " . $e->getMessage());
        }
    }

    private function processIncomingMessage($message, $payload)
    {
        try {
            $from = $message['from'];
            $waId = $message['id'];
            $rawType = $message['type'];
            $body = '';

            // Extraer nombre de perfil de forma segura
            $profileName = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? 'Usuario WhatsApp';

            // Mapeo de tipos para la BD
            $typeMapping = [
                'text' => 'texto',
                'image' => 'imagen',
                'document' => 'documento',
                'audio' => 'audio',
                'template' => 'plantilla',
                'reaction' => 'reaccion'
            ];
            $type = $typeMapping[$rawType] ?? 'texto';

            if ($rawType === 'text') {
                $body = $message['text']['body'];
            } elseif ($rawType === 'image') {
                $body = $message['image']['id'];
            }

            Log::channel('whatsapp')->info("Procesando mensaje de $from: $body");

            // 1. Buscar o crear el CONTACTO
            $contacto = WhatsappContacto::where('wa_id', $from)->first();

            if (!$contacto) {
                // Limpiar el número para la búsqueda (quitar +)
                $cleanFrom = str_replace('+', '', $from);
                $cliente = Cliente::where('telefono_principal', 'LIKE', "%$cleanFrom%")->first();

                $contacto = WhatsappContacto::create([
                    'wa_id' => $from,
                    'nombre_wa' => $profileName,
                    'numero_celular' => $from,
                    'cliente_id' => $cliente ? $cliente->id : null
                ]);
                Log::channel('whatsapp')->info("Nuevo contacto creado: ID {$contacto->id}");
            }

            // 2. Buscar conversación activa
            $conversacion = WhatsappConversacion::where('contacto_id', $contacto->id)
                ->where('estado', '!=', 'cerrado')
                ->latest()
                ->first();

            if (!$conversacion) {
                $conversacion = WhatsappConversacion::create([
                    'contacto_id' => $contacto->id,
                    'cliente_id' => $contacto->cliente_id,
                    'estado' => 'nuevo',
                    'last_message_at' => now()
                ]);
                Log::channel('whatsapp')->info("Nueva conversación creada: ID {$conversacion->id}");
            }

            // Actualizar metadata de conversación
            if ($conversacion->estado !== 'asignado') {
                $conversacion->increment('mensajes_sin_leer');
            }
            $conversacion->update(['last_message_at' => now()]);

            // 3. Guardar el mensaje
            WhatsappMensaje::create([
                'conversacion_id' => $conversacion->id,
                'direccion' => 'entrante',
                'tipo' => $type,
                'contenido' => $body,
                'wa_message_id' => $waId,
                'estado' => 'leido'
            ]);

            Log::channel('whatsapp')->info("Mensaje guardado en BD.");

            // 4. Lógica de Bienvenida
            if ($conversacion->wasRecentlyCreated && $conversacion->estado === 'nuevo') {
                $service = new WhatsappService();
                $displayName = $contacto->cliente ? $contacto->cliente->nombre : $contacto->nombre_wa;

                $menuText = "¡Hola " . $displayName . "! Bienvenido a Aybar Corp. 🌿\n\nPor favor, elige una opción:\n1. Consultas Generales (ATC)\n2. Pagos y Evidencias\n3. Firmas y Letras";
                $service->sendText($from, $menuText);

                $conversacion->update(['estado' => 'en_menu']);
            }

        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Error procesando mensaje en job: " . $e->getMessage());
            throw $e;
        }
    }

    private function updateMessageStatus($status)
    {
        try {
            $waId = $status['id'];
            $metaStatus = $status['status']; // sent, delivered, read, failed

            // Mapeo hacia nuestros términos en español (ENUM de la BD)
            $mapping = [
                'sent' => 'enviado',
                'delivered' => 'entregado',
                'read' => 'leido',
                'failed' => 'fallido'
            ];

            $nuevoEstado = $mapping[$metaStatus] ?? 'enviado';

            $mensaje = WhatsappMensaje::where('wa_message_id', $waId)->first();

            if ($mensaje) {
                $mensaje->update(['estado' => $nuevoEstado]);
                Log::channel('whatsapp')->info("Mensaje $waId actualizado a $nuevoEstado");
            }
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Error actualizando estado en job: " . $e->getMessage());
            throw $e;
        }
    }
}
