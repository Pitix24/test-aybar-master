<?php

namespace App\Console\Commands\Gmail;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\WatchRequest;

class WatchIndecopiCommand extends Command
{
    // El comando que ejecutaremos en la terminal
    protected $signature = 'gmail:watch-indecopi';
    protected $description = 'Activa la suscripción Push de Gmail para el buzón consolidado de Indecopi';

    public function handle()
    {
        $this->info('Iniciando activación de vigilancia en Gmail...');

        try {
            // 1. Validar que tengamos el token de acceso perpetuo (Refresh Token)
            $refreshToken = config('services.google.refresh_token');

            if (!$refreshToken) {
                $this->error('Falta el GOOGLE_REFRESH_TOKEN en el .env. Debes generar uno primero.');
                return;
            }

            // 2. Configurar el Cliente de Google de forma segura usando config()
            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->refreshToken($refreshToken);

            // 3. Instanciar el servicio de Gmail
            $service = new Gmail($client);

            // 4. Preparar la petición hacia Pub/Sub
            $watchRequest = new WatchRequest();
            $watchRequest->setTopicName(config('services.google.pubsub_topic'));
            $watchRequest->setLabelIds(['INBOX']);

            // 5. Ejecutar la orden de vigilancia sobre el buzón consolidado
            $response = $service->users->watch(config('services.google.inbox'), $watchRequest);

            // 6. Mensajes de Éxito
            $this->info('¡Vigilancia activada exitosamente!');
            $this->line('History ID actual: ' . $response->getHistoryId());

            // Gmail desactiva el watch cada 7 días, mostramos cuándo expira:
            $expiracion = date('Y-m-d H:i:s', $response->getExpiration() / 1000);
            $this->line("Expiración del permiso: {$expiracion}");

            Log::info("Vigilancia activada para Gmail Indecopi. History ID: {$response->getHistoryId()} | Expira: {$expiracion}");
            
        } catch (\Exception $e) {
            $this->error('Error al activar vigilancia: ' . $e->getMessage());
            Log::error('Error en WatchIndecopiCommand: ' . $e->getMessage());
        }
    }
}
