<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\ClienteDocumento;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
// use App\Services\AybarSlinService; // Para cuando agregues la validación
// use Illuminate\Support\Facades\Auth;

class ClienteDocumentoController extends Controller
{
    /**
     * Sirve el PDF de un documento para el visor seguro.
     */
    public function stream($id)
    {
        // 1. Buscar el documento activo y que sea solo lectura
        $documento = ClienteDocumento::where('activo', true)
            ->where('solo_lectura', true)
            ->findOrFail($id);

        // =====================================================================
        // [MEJORA DE SEGURIDAD RECOMENDADA]
        // Aquí deberías inyectar el AybarSlinService, obtener los $proyectosPermitidos
        // del usuario actual (Auth::user()) y verificar:
        // if (!in_array($documento->proyecto_id, $proyectosPermitidos)) {
        //     abort(403, 'No tienes acceso a este documento.');
        // }
        // =====================================================================

        $archivoPdf = $documento->archivoPdf;

        if (!$archivoPdf) {
            abort(404, 'PDF no encontrado');
        }

        $path = $archivoPdf->path;

        // 2. Búsqueda inteligente de discos (Copiado de la lógica de Reglamentos)
        $disk = null;
        if (Storage::disk('local')->exists($path)) {
            $disk = 'local'; // Disco seguro privado (Recomendado)
        } elseif (Storage::disk('public')->exists($path)) {
            $disk = 'public'; // Fallback por si acaso hay archivos antiguos
        } else {
            abort(404, 'Archivo físico no encontrado en el servidor');
        }

        // 3. Transmisión optimizada por Stream
        $stream = Storage::disk($disk)->readStream($path);

        $response = new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            // Headers for security
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);

        return $response;
    }
}
