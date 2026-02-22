<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\Cliente;

class WhatsappController extends Controller
{
    /**
     * Verificación del Webhook (Meta requiere esto una sola vez para validar el dominio)
     */
    public function verifyWebhook(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token', 'aybar_crm_secret_token');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::channel('whatsapp')->info('WhatsApp Webhook verificado correctamente.');
                return response($challenge, 200);
            }
        }

        return response('Forbidden', 403);
    }

    /**
     * Recepción de mensajes y estados (POST)
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        // Log RAW para ver exactamente qué manda Meta sin normalizaciones
        Log::channel('whatsapp')->info('--- WEBHOOK ENTRANTE ---');
        Log::channel('whatsapp')->debug('Payload:', $payload);

        if (isset($payload['entry'][0]['changes'][0]['value'])) {
            $value = $payload['entry'][0]['changes'][0]['value'];

            // 1. Manejar Mensajes Entrantes
            if (isset($value['messages'][0])) {
                Log::channel('whatsapp')->info('Mensaje detectado.');
                $this->processIncomingMessage($value['messages'][0], $payload);
            }

            // 2. Manejar Estados (Enviado, Entregado, Leído)
            if (isset($value['statuses'][0])) {
                Log::channel('whatsapp')->info('Estado detectado.');
                $this->updateMessageStatus($value['statuses'][0]);
            }
        }

        return response('OK', 200);
    }

    private function processIncomingMessage($message, $payload)
    {
        try {
            $from = $message['from'];
            $waId = $message['id'];
            $type = $message['type'];
            $body = '';

            // Extraer nombre de perfil de forma segura
            $profileName = $payload['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? 'Usuario WhatsApp';

            $rawType = $message['type'];
            $body = '';

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
            $contacto = \App\Models\WhatsappContacto::where('wa_id', $from)->first();

            if (!$contacto) {
                // Limpiar el número para la búsqueda (quitar +)
                $cleanFrom = str_replace('+', '', $from);
                $cliente = \App\Models\Cliente::where('telefono_principal', 'LIKE', "%$cleanFrom%")->first();

                $contacto = \App\Models\WhatsappContacto::create([
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
                $service = new \App\Services\WhatsappService();
                $displayName = $contacto->cliente ? $contacto->cliente->nombre : $contacto->nombre_wa;

                $menuText = "¡Hola " . $displayName . "! Bienvenido a Aybar Corp. 🌿\n\nPor favor, elige una opción:\n1. Consultas Generales (ATC)\n2. Pagos y Evidencias\n3. Firmas y Letras";
                $service->sendText($from, $menuText);

                $conversacion->update(['estado' => 'en_menu']);
            }

        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Error procesando mensaje: " . $e->getMessage());
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
            Log::channel('whatsapp')->error("Error actualizando estado: " . $e->getMessage());
        }
    }
}
