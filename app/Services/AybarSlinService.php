<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AybarSlinService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.aybar_slin.url'), '/');
    }

    /**
     * Obtener información del cliente por DNI
     */
    public function getCliente(string $dni): ?array
    {
        return cache()->remember(
            "aybar_slin.cliente.{$dni}",
            now()->addMinutes(15),
            function () use ($dni) {
                try {
                    // ⚡ Timeout reducido: 3s en lugar de 10s
                    $response = Http::timeout(3)
                        ->connectTimeout(2)
                        ->get("{$this->baseUrl}/cliente/{$dni}");

                    return $response->successful() ? $response->json() : null;
                } catch (\Exception $e) {
                    Log::warning("AybarSlinService@getCliente fallback", [
                        'dni' => $dni,
                        'error' => $e->getMessage(),
                    ]);
                    return null;
                }
            }
        );
    }
    /**
     * Obtener lotes asociados a un cliente y empresa
     */
    public function getLotes(string $idCliente, string $idEmpresa): array
    {
        return cache()->remember(
            "aybar_slin.lotes.{$idCliente}.{$idEmpresa}",
            now()->addMinutes(15),
            function () use ($idCliente, $idEmpresa) {
                try {
                    // ⚡ Timeout reducido: 3s en lugar de 10s
                    $response = Http::timeout(3)
                        ->connectTimeout(2)
                        ->get("{$this->baseUrl}/lotes", [
                            'id_cliente' => $idCliente,
                            'id_empresa' => $idEmpresa,
                        ]);

                    if (!$response->successful()) return [];

                    $data = $response->json();
                    if (isset($data['data']) && is_array($data['data'])) return $data['data'];
                    return is_array($data) ? $data : [];
                } catch (\Exception $e) {
                    Log::warning("AybarSlinService@getLotes fallback", [
                        'id_cliente' => $idCliente,
                        'error' => $e->getMessage(),
                    ]);
                    return [];
                }
            }
        );
    }

    /**
     * Obtener el cronograma y estado de cuenta unificado
     */
    public function getCronogramaEstadoCuenta(array $params): ?array
    {
        try {
            $response = Http::timeout(20)->get("{$this->baseUrl}/cuota-estado-cuenta", [
                'empresa' => $params['id_empresa'],
                'lote' => $params['lote'],
                'cliente' => $params['id_cliente'],
                'contrato' => $params['contrato'] ?? '',
                'servicio' => $params['servicio'] ?? '02',
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AybarSlinService@getCronogramaEstadoCuenta: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ver y descargar el comprobante en PDF decodificado desde Base64
     */
    public function verComprobante(\Illuminate\Http\Request $request)
    {
        $empresa = $request->query('empresa');
        $comprobante = $request->query('comprobante');

        if (!$empresa || !$comprobante) {
            abort(400, 'Parámetros inválidos');
        }

        try {
            $response = Http::timeout(20)->get("{$this->baseUrl}/comprobante", [
                'empresa' => $empresa,
                'comprobante' => $comprobante,
            ]);

            if ($response->failed()) {
                abort(404, 'No se pudo obtener el comprobante');
            }

            $json = $response->json();

            if (empty($json['base64'])) {
                abort(500, 'Comprobante inválido');
            }

            $pdfBinary = base64_decode($json['base64']);

            return response($pdfBinary, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="comprobante.pdf"');
        } catch (\Exception $e) {
            Log::error("AybarSlinService@verComprobante: " . $e->getMessage());
            abort(500, 'Error interno al procesar el comprobante');
        }
    }

    /**
     * Guardar evidencia de pago en el sistema externo
     */
    public function postGuardarEvidencia(array $params): ?array
    {
        try {
            $url = config('services.slin.url') ?? "{$this->baseUrl}/evidencia";

            $response = Http::acceptJson()
                ->contentType('application/json')
                ->timeout(30)
                ->post($url, $params);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AybarSlinService@postGuardarEvidencia: " . $e->getMessage());
            return null;
        }
    }
}
