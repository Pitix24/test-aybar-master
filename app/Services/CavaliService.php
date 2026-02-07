<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CavaliService
{
    public function obtenerConstanciaCancelacion(string $numeroLetra): array
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
                'headers' => [
                    'Expect' => '',
                ],
            ])
            ->withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction'   => '""',
            ])
            ->withBody($soapBody, 'text/xml')
            ->post(config('services.canvia.url'));

        // DEBUG SI FALLA
        if ($response->status() !== 200) {
            logger()->error('SOAP ERROR', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            throw new \Exception(
                'Error HTTP SOAP: ' . $response->status()
            );
        }

        return $this->extraerBase64($response->body());
    }

    private function extraerBase64(string $xml): array
    {
        preg_match(
            '/<codigoOperacion>(.*?)<\/codigoOperacion>/',
            $xml,
            $codigoMatch
        );

        preg_match(
            '/<formatoBase64>(.*?)<\/formatoBase64>/s',
            $xml,
            $base64Match
        );

        return [
            'codigo' => $codigoMatch[1] ?? '',
            'base64' => $base64Match[1] ?? '',
        ];
    }
}
