<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\Cliente;
use App\Jobs\ProcessIncomingWhatsapp;

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
