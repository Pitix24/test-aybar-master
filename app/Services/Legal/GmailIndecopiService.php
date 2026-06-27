<?php

namespace App\Services\Legal;

use Google\Client;
use Google\Service\Gmail;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\Log;

class GmailIndecopiService
{
    protected $client;
    protected $service;
    protected $inbox;

    public function __construct()
    {
        // 1. Inicializar el cliente usando el Token Perpetuo
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->refreshToken(config('services.google.refresh_token'));

        $this->service = new Gmail($this->client);
        $this->inbox = config('services.google.inbox');
    }

    /**
     * Descarga un correo específico desde Gmail por su ID
     */
    public function descargarCorreo(string $messageId)
    {
        try {
            // El formato 'full' nos trae los headers y el cuerpo completo
            $mensaje = $this->service->users_messages->get($this->inbox, $messageId, ['format' => 'full']);
            return $mensaje;
        } catch (\Exception $e) {
            Log::error("Error descargando correo {$messageId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extrae un Header específico (ej: 'To', 'From', 'Subject')
     */
    public function obtenerCabecera($headers, string $nombreCabecera)
    {
        foreach ($headers as $header) {
            if (strtolower($header->getName()) === strtolower($nombreCabecera)) {
                return $header->getValue();
            }
        }
        return null;
    }

    /**
     * Lógica de Match: Cruzar el correo receptor con la Razón Social
     */
    public function identificarUnidadNegocio($emailOriginal)
    {
        if (!$emailOriginal) return null;

        // A veces el header To viene así: "Lotes SAC <notificaciones@aybarsac.com>"
        // Esta pequeña Regex limpia todo y extrae solo el correo limpio.
        preg_match('/<([^>]+)>/', $emailOriginal, $matches);
        $correoLimpio = trim($matches[1] ?? $emailOriginal);

        // Buscamos en la tabla usando el campo que creamos en la Fase 3
        return UnidadNegocio::where('email_interno', $correoLimpio)
            ->orWhere('email_alias', $correoLimpio)
            ->first();
    }

    /**
     * Decodifica el cuerpo del correo (Soporta HTML y partes anidadas complejas)
     */
    public function decodificarCuerpo($payload)
    {
        $bodyData = $this->extraerDataDePartes($payload);
        
        if (!$bodyData) {
            return "No se encontró contenido.";
        }

        // 1. Decodificación de Google Base64
        $bodyDecoded = base64_decode(strtr($bodyData, '-_', '+/'));
        
        // 2. Convertir saltos de línea HTML a texto
        $textoLimpio = preg_replace('/<br\s*\/?>/i', "\n", $bodyDecoded);
        
        // 3. Quitar etiquetas HTML (tablas, divs, etc.)
        $textoLimpio = strip_tags($textoLimpio);
        
        // 4. Traducir caracteres raros (&Oacute; -> Ó, &ntilde; -> ñ)
        $textoLimpio = html_entity_decode($textoLimpio, ENT_QUOTES, 'UTF-8');
        
        // 5. Eliminar espacios y saltos de línea excesivos (Comprime el texto)
        $textoLimpio = preg_replace("/[\r\n]+/", "\n", $textoLimpio); // Múltiples saltos a uno solo
        $textoLimpio = preg_replace('/[ \t]+/', ' ', $textoLimpio);   // Múltiples espacios a uno solo
        
        return trim($textoLimpio);
    }

    /**
     * Aplica Expresiones Regulares (Regex) para extraer la metadata del texto limpio.
     */
    public function extraerDatosDelCuerpo($textoLimpio)
    {
        $datos = [
            'numero_referencia'  => null,
            'fecha_notificacion' => null,
            'enlace_plataforma'  => null,
        ];

        // 1. Extraer Documento de Referencia (Ej: 1580-2025/CC2)
        // \s* significa "cualquier cantidad de espacios o saltos de línea"
        if (preg_match('/Documento\s+de\s+referencia\s*:\s*([A-Z0-9\-\/]+)/i', $textoLimpio, $matches)) {
            $datos['numero_referencia'] = trim($matches[1]);
        }

        // 2. Extraer Fecha (Ej: 2026-06-26)
        // .*? significa "ignora cualquier texto, tilde o salto de línea hasta encontrar los dos puntos"
        if (preg_match('/Fecha\s+de\s+notificaci.*?:\s*([0-9\-]+)/is', $textoLimpio, $matches)) {
            $datos['fecha_notificacion'] = trim($matches[1]);
        }

        // 3. Extraer Enlace a la plataforma
        if (preg_match('/Enlace\s+de\s+la\s+Plataforma.*?(http[s]?:\/\/[^\s]+)/i', $textoLimpio, $matches)) {
            $datos['enlace_plataforma'] = trim($matches[1]);
        }

        return $datos;
    }

    /**
     * Función recursiva para escarbar en correos con múltiples adjuntos o formatos
     */
    private function extraerDataDePartes($part)
    {
        if ($part->getBody() && $part->getBody()->getData()) {
            return $part->getBody()->getData();
        }

        if ($part->getParts()) {
            // 1. Intentar buscar texto plano directo
            foreach ($part->getParts() as $p) {
                if ($p->getMimeType() === 'text/plain' && $p->getBody() && $p->getBody()->getData()) {
                    return $p->getBody()->getData();
                }
            }
            // 2. Si no hay texto plano, buscamos HTML
            foreach ($part->getParts() as $p) {
                if ($p->getMimeType() === 'text/html' && $p->getBody() && $p->getBody()->getData()) {
                    return $p->getBody()->getData();
                }
            }
            // 3. Si sigue oculto (ej. tiene adjuntos), hacemos recursión hacia adentro
            foreach ($part->getParts() as $p) {
                if (strpos($p->getMimeType(), 'multipart') !== false) {
                    $data = $this->extraerDataDePartes($p);
                    if ($data) return $data;
                }
            }
        }
        return null;
    }

    /**
     * Busca el ID del último correo recibido en el Inbox
     */
    public function obtenerUltimoCorreoId()
    {
        $optParams = [
            'maxResults' => 1,
            'labelIds' => ['INBOX']
        ];
        
        $lista = $this->service->users_messages->listUsersMessages($this->inbox, $optParams);
        
        if ($lista->getMessages() && count($lista->getMessages()) > 0) {
            return $lista->getMessages()[0]->getId();
        }
        
        return null;
    }
}