<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PowerBiService
{
    /**
     * Duración del caché del access token de Azure (en segundos).
     * El token original dura ~3600s, cacheamos por 3300s (55 min) para renovar antes de expirar.
     */
    private const ACCESS_TOKEN_CACHE_TTL = 3300;

    /**
     * Obtener los datos de embed para un reporte específico.
     *
     * @param string $reportKey Clave del reporte (ej: 'ticket', 'cita', 'cliente')
     * @return array Datos necesarios para renderizar el reporte en el frontend
     */
    public function getEmbedData(string $reportKey): array
    {
        $reportId    = config("powerbi.reports.{$reportKey}");
        $workspaceId = config('powerbi.workspace_id');
        $pageName    = config("powerbi.pages.{$reportKey}");

        // Si faltan credenciales o reportId, retornar como no configurado
        if (!$reportId || !$workspaceId || !config('powerbi.tenant_id') || !config('powerbi.client_id')) {
            return [
                'configured' => false,
                'message'    => 'Power BI no está configurado. Revisa las variables POWERBI_* en el archivo .env',
            ];
        }

        try {
            $accessToken = $this->getAccessToken();
            $embedToken  = $this->generateEmbedToken($accessToken, $workspaceId, $reportId);
            $embedUrl    = "https://app.powerbi.com/reportEmbed?reportId={$reportId}&groupId={$workspaceId}";

            return [
                'configured' => true,
                'embedToken' => $embedToken,
                'embedUrl'   => $embedUrl,
                'reportId'   => $reportId,
                'pageName'   => $pageName,
            ];
        } catch (\Exception $e) {
            Log::error('PowerBI Embed Error', [
                'reportKey' => $reportKey,
                'error'     => $e->getMessage(),
            ]);

            return [
                'configured' => false,
                'message'    => 'Error al conectar con Power BI: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generar solo un embed token fresco (para refresh desde el frontend).
     *
     * @param string $reportKey Clave del reporte
     * @return array Token fresco o error
     */
    public function refreshToken(string $reportKey): array
    {
        $reportId    = config("powerbi.reports.{$reportKey}");
        $workspaceId = config('powerbi.workspace_id');

        if (!$reportId || !$workspaceId) {
            return ['success' => false, 'message' => 'Reporte no configurado'];
        }

        try {
            $accessToken = $this->getAccessToken();
            $embedToken  = $this->generateEmbedToken($accessToken, $workspaceId, $reportId);

            return [
                'success'    => true,
                'embedToken' => $embedToken,
            ];
        } catch (\Exception $e) {
            Log::error('PowerBI Token Refresh Error', [
                'reportKey' => $reportKey,
                'error'     => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtener access token de Azure Entra ID con caché.
     * Usa OAuth2 client_credentials flow con el Service Principal.
     */
    private function getAccessToken(): string
    {
        return Cache::remember('powerbi_access_token', self::ACCESS_TOKEN_CACHE_TTL, function () {
            $tenantId     = config('powerbi.tenant_id');
            $clientId     = config('powerbi.client_id');
            $clientSecret = config('powerbi.client_secret');
            $resourceUrl  = config('powerbi.resource_url');

            $tokenUrl = config('powerbi.authority_url') . "{$tenantId}/oauth2/v2.0/token";

            $response = Http::asForm()->post($tokenUrl, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'scope'         => $resourceUrl,
            ]);

            if (!$response->successful()) {
                $error = $response->json('error_description', $response->body());
                throw new \RuntimeException("Error al obtener access token de Azure: {$error}");
            }

            return $response->json('access_token');
        });
    }

    /**
     * Generar un Embed Token para un reporte específico.
     * Llama a POST /groups/{workspaceId}/reports/{reportId}/GenerateToken
     */
    private function generateEmbedToken(string $accessToken, string $workspaceId, string $reportId): string
    {
        $apiUrl = config('powerbi.api_url');
        $url    = "{$apiUrl}groups/{$workspaceId}/reports/{$reportId}/GenerateToken";

        $response = Http::withToken($accessToken)->post($url, [
            'accessLevel' => 'View',
        ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', $response->body());
            throw new \RuntimeException("Error al generar embed token: {$error}");
        }

        return $response->json('token');
    }

    /**
     * Limpiar el caché del access token (útil si se cambian credenciales).
     */
    public function clearTokenCache(): void
    {
        Cache::forget('powerbi_access_token');
    }
}
