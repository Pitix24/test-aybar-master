<?php

namespace App\Http\Controllers\Erp\Marketing;

use App\Http\Controllers\Controller;
use App\Models\ClienteDocumento;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClienteDocumentoController extends Controller
{
    /**
     * Sirve el PDF de un documento de cliente para el ERP (admins).
     * Sin restricción de solo_lectura — el admin puede ver cualquier documento.
     */
    public function stream($id)
    {
        $documento = ClienteDocumento::where('activo', true)->findOrFail($id);

        $archivoPdf = $documento->archivoPdf;

        if (!$archivoPdf) {
            abort(404, 'PDF no encontrado');
        }

        $path = $archivoPdf->path;

        $disk = null;
        if (Storage::disk('local')->exists($path)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($path)) {
            $disk = 'public';
        } else {
            abort(404, 'Archivo físico no encontrado en el servidor');
        }

        $stream = Storage::disk($disk)->readStream($path);

        $response = new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $archivoPdf->nombre_original . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);

        return $response;
    }
}
