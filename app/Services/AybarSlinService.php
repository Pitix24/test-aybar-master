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
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/cliente/{$dni}");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AybarSlinService@getCliente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener lotes de un cliente en una empresa específica
     */
    public function getLotes(string $idCliente, string $idEmpresa): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/lotes", [
                'id_cliente' => $idCliente,
                'id_empresa' => $idEmpresa,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $data = $response->json();

            // Normalización según diferentes estructuras de respuesta detectadas
            if (isset($data['data']) && is_array($data['data'])) {
                return $data['data'];
            }

            return is_array($data) ? $data : [];
        } catch (\Exception $e) {
            Log::error("AybarSlinService@getLotes: " . $e->getMessage());
            return [];
        }
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
}
