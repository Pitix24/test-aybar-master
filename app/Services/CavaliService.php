<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class CavaliService
{
    /**
     * OPTIMIZADO: Valida existencia usando búsqueda simple de texto (strpos).
     * Evita el procesamiento de expresiones regulares pesadas sobre el Base64.
     */
    public function existeConstancia(string $numeroLetra): bool
    {
        try {
            $xml = $this->obtenerXmlBruto($numeroLetra);

            // Verificamos si la etiqueta de apertura existe y si hay contenido después de ella
            return strpos($xml, '<formatoBase64>') !== false &&
                strpos($xml, '<formatoBase64></formatoBase64>') === false;
        } catch (Exception $e) {
            logger()->warning("CavaliService: No se pudo verificar la letra {$numeroLetra}. " . $e->getMessage());
            return false;
        }
    }

    /**
     * Consulta completa: Devuelve el array procesado con [codigo, base64, error].
     */
    public function consultar(string $numeroLetra): array
    {
        try {
            $xml = $this->obtenerXmlBruto($numeroLetra);
            return $this->procesarXml($xml);
        } catch (Exception $e) {
            return [
                'codigo' => '',
                'base64' => '',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Centraliza la petición HTTP para obtener el cuerpo del XML sin procesar.
     */
    private function obtenerXmlBruto(string $numeroLetra): string
    {
        $soapBody = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                  xmlns:rem="http://remote.soap.digitall.canvia.com/">
    <soapenv:Header/>
    <soapenv:Body>
        <rem:obtenerConstanciaCancelacion>
            <arg0>
                <numeroLetra>{$numeroLetra}</numeroLetra>
            </arg0>
        </rem:obtenerConstanciaCancelacion>
    </soapenv:Body>
</soapenv:Envelope>
XML;

        $response = Http::withBasicAuth(
            config('services.canvia.user'),
            config('services.canvia.password')
        )
            ->withOptions([
                'verify' => false,
                'timeout' => 30,
                'http_errors' => false,
            ])
            ->withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '""',
            ])
            ->withBody($soapBody, 'text/xml')
            ->post(config('services.canvia.url'));

        if ($response->status() !== 200) {
            throw new Exception('Error HTTP ' . $response->status());
        }

        return $response->body();
    }

    /**
     * Extrae los datos relevantes del XML.
     */
    private function procesarXml(string $xml): array
    {
        // Solo aquí usamos regex porque necesitamos extraer los valores
        $xmlClean = str_replace(["\n", "\r", "\t"], '', $xml);

        preg_match('/<codigoOperacion>(.*?)<\/codigoOperacion>/', $xmlClean, $codigoMatch);
        preg_match('/<formatoBase64>(.*?)<\/formatoBase64>/', $xmlClean, $base64Match);
        preg_match('/<mensajeError>(.*?)<\/mensajeError>/', $xmlClean, $errorMatch);

        return [
            'codigo' => trim($codigoMatch[1] ?? ''),
            'base64' => trim($base64Match[1] ?? ''),
            'error' => trim($errorMatch[1] ?? null),
        ];
    }
}
