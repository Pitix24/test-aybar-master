<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected $token;
    protected $phoneId;
    protected $baseUrl;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
        $this->baseUrl = "https://graph.facebook.com/v21.0/" . $this->phoneId;
    }

    /**
     * Envía un mensaje de texto simple
     */
    public function sendText($to, $text)
    {
        return $this->apiCall([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $text]
        ]);
    }

    /**
     * Envía una plantilla (HSM)
     */
    public function sendTemplate($to, $templateName, $languageCode = 'es', $components = [])
    {
        return $this->apiCall([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $languageCode],
                'components' => $components
            ]
        ]);
    }

    /**
     * Envía una imagen por URL pública con caption opcional
     */
    public function sendImage($to, string $imageUrl, string $caption = '')
    {
        $image = ['link' => $imageUrl];
        if ($caption !== '') {
            $image['caption'] = $caption;
        }

        return $this->apiCall([
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'image',
            'image' => $image,
        ]);
    }

    /**
     * Lógica central de llamadas al API
     */
    private function apiCall($payload)
    {
        try {
            $response = Http::withToken($this->token)
                ->post($this->baseUrl . "/messages", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::channel('whatsapp')->error('Error en WhatsApp API:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);
            // También lo mandamos al log general para verlo fácil
            Log::error('[WSP API ERROR] Status: ' . $response->status() . ' | Body: ' . $response->body());

            return false;
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Excepción en WhatsApp Service: ' . $e->getMessage());
            return false;
        }
    }
}
