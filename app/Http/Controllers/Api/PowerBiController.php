<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PowerBiService;
use Illuminate\Http\JsonResponse;

class PowerBiController extends Controller
{
    /**
     * Refrescar el embed token de un reporte Power BI.
     *
     * El frontend llama a este endpoint antes de que el token actual expire
     * (~55 minutos) para mantener el reporte interactivo sin recargar la página.
     *
     * @param PowerBiService $service
     * @param string $reportKey Clave del reporte (ej: 'ticket', 'cita')
     * @return JsonResponse
     */
    public function getToken(PowerBiService $service, string $reportKey): JsonResponse
    {
        // Validar que la clave del reporte existe en la configuración
        $validKeys = array_keys(config('powerbi.reports', []));

        if (!in_array($reportKey, $validKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Reporte no válido',
            ], 404);
        }

        $result = $service->refreshToken($reportKey);

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }
}
