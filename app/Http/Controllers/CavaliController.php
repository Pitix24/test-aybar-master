<?php

namespace App\Http\Controllers;

use App\Services\CavaliService;

class CavaliController extends Controller
{
    public function verLetra(string $numeroLetra, CavaliService $service)
    {
        if (empty($numeroLetra)) {
            return response()->json([
                'error' => 'NumeroLetra es obligatorio'
            ], 422);
        }

        $result = $service->obtenerConstanciaCancelacion($numeroLetra);

        if ($result['codigo'] !== '001' || empty($result['base64'])) {
            return response()->json([
                'error' => 'No se pudo obtener la constancia'
            ], 400);
        }

        $pdf = base64_decode($result['base64'], true);

        if ($pdf === false) {
            return response()->json([
                'error' => 'PDF inválido'
            ], 500);
        }

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="constancia.pdf"',
        ]);
    }
}
