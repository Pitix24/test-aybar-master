<?php

namespace App\Http\Controllers;

use App\Services\CavaliService;

class CavaliController extends Controller
{
    public function verLetra(string $numeroLetra, CavaliService $service)
    {
        if (empty($numeroLetra)) {
            return response()->json(['error' => 'El número de letra es obligatorio'], 422);
        }

        $result = $service->consultar($numeroLetra);

        if ($result['codigo'] !== '001' || empty($result['base64'])) {
            return response()->json([
                'error' => $result['error'] ?? 'No se pudo obtener la constancia de Cavali',
                'codigo' => $result['codigo']
            ], 400);
        }

        $pdf = base64_decode($result['base64'], true);

        if ($pdf === false) {
            return response()->json(['error' => 'El archivo PDF recibido no es válido'], 500);
        }

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="constancia_' . $numeroLetra . '.pdf"',
        ]);
    }

    public function validarLetra(string $numeroLetra, CavaliService $service)
    {
        if (empty($numeroLetra)) {
            return response()->json(['existe' => false, 'error' => 'NumeroLetra es obligatorio'], 422);
        }

        $existe = $service->existeConstancia($numeroLetra);

        return response()->json([
            'numeroLetra' => $numeroLetra,
            'existe' => $existe
        ]);
    }
}
