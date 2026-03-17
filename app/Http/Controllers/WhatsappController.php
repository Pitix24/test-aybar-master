<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\Cliente;
use App\Jobs\ProcessIncomingWhatsapp;
use App\Models\WhatsappContacto;

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
        // 1️⃣ Validación de firma X-Hub-Signature-256
        if (!$this->validateSignature($request)) {
            Log::channel('whatsapp')->warning('Firma de webhook inválida.');
            return response('Invalid signature', 403);
        }

        $payload = $request->all();

        // Log RAW para ver exactamente qué manda Meta
        Log::channel('whatsapp')->info('--- WEBHOOK ENTRANTE (Encolado) ---');
        Log::channel('whatsapp')->debug('Payload:', $payload);

        // 2️⃣ Uso de Jobs (colas) para procesamiento asíncrono
        try {
            ProcessIncomingWhatsapp::dispatch($payload);
            return response('OK', 200);
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Error al despachar job: " . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Guarda un mensaje interno enviado desde n8n
     */
    public function storeInternal(Request $request)
    {
        try {
            $validated = $request->validate([
                'wa_id' => 'required|string',
                'nombre_wa' => 'nullable|string',
                'direccion' => 'required|in:entrante,saliente',
                'tipo' => 'required|string',
                'contenido' => 'required|string',
                'wa_message_id' => 'required|string',
            ]);

            $wa_id = $validated['wa_id'];
            $profileName = $validated['nombre_wa'] ?? 'Usuario WhatsApp';

            // 1. Buscar o crear el CONTACTO
            $contacto = WhatsappContacto::where('wa_id', $wa_id)->first();
            if (!$contacto) {
                $cleanFrom = str_replace('+', '', $wa_id);
                $cliente = Cliente::where('telefono_principal', 'LIKE', "%$cleanFrom%")->first();
                $contacto = WhatsappContacto::create([
                    'wa_id' => $wa_id,
                    'nombre_wa' => $profileName,
                    'numero_celular' => $wa_id,
                    'cliente_id' => $cliente ? $cliente->id : null
                ]);
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
            }

            // Actualizar metadata
            if ($validated['direccion'] === 'entrante' && $conversacion->estado !== 'asignado') {
                $conversacion->increment('mensajes_sin_leer');
            }
            $conversacion->update(['last_message_at' => now()]);

            // 3. Guardar el mensaje (revisar si ya existe por wa_message_id para evitar duplicados de n8n)
            $mensaje = WhatsappMensaje::updateOrCreate(
                ['wa_message_id' => $validated['wa_message_id']],
                [
                    'conversacion_id' => $conversacion->id,
                    'direccion' => $validated['direccion'],
                    'tipo' => $validated['tipo'],
                    'contenido' => $validated['contenido'],
                    'estado' => 'leido'
                ]
            );

            return response()->json([
                'status' => 'success',
                'message_id' => $mensaje->id,
                'conversacion_id' => $conversacion->id
            ]);

        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Error en storeInternal: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Valida la firma enviada por Meta en el header X-Hub-Signature-256
     */
    private function validateSignature(Request $request)
    {
        $signature = $request->header('X-Hub-Signature-256');

        if (!$signature) {
            return false;
        }

        // El header viene en formato sha256={hash}
        $parts = explode('=', $signature);
        if (count($parts) !== 2) {
            return false;
        }

        $receivedHash = $parts[1];
        $appSecret = config('services.whatsapp.app_secret');

        if (!$appSecret) {
            Log::channel('whatsapp')->error('WHATSAPP_APP_SECRET no está configurado.');
            return false;
        }

        $expectedHash = hash_hmac('sha256', $request->getContent(), $appSecret);

        return hash_equals($expectedHash, $receivedHash);
    }
}
