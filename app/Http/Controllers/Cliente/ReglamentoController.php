<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Reglamento;
use Illuminate\Support\Facades\Storage;

class ReglamentoController extends Controller
{
    public function index()
    {
        return view('modules.cliente.reglamento');
    }

    /**
     * Transmite el archivo PDF privado de forma segura.
     * Compatible con archivos en disco 'local' (nuevos) y 'public' (legados).
     */
    public function stream($id)
    {
        // 1. Buscamos el reglamento con su archivo
        $reglamento = Reglamento::with('archivoPdf')->findOrFail($id);

        if (!$reglamento->archivoPdf) {
            abort(404, 'Este reglamento no tiene archivo adjunto.');
        }

        $path = $reglamento->archivoPdf->path;

        // 2. Intentamos primero el disco 'local' (privado - archivos nuevos)
        if (Storage::disk('local')->exists($path)) {
            $fileContent = Storage::disk('local')->get($path);
        }
        // 3. Fallback: disco 'public' (compatibilidad con registros creados antes de la migración)
        elseif (Storage::disk('public')->exists($path)) {
            $fileContent = Storage::disk('public')->get($path);
        }
        else {
            abort(404, 'El archivo físico no existe o fue removido del servidor.');
        }

        // OPCIONAL SUPER SEGURIDAD: Aquí puedes verificar si el Auth::user() pertenece
        // al proyecto asociado a este reglamento para evitar que adivinen IDs.

        // 4. Retornamos la respuesta binaria con cabeceras de protección
        return response($fileContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $reglamento->archivoPdf->nombre_original . '"',
            'Cache-Control'       => 'private, max-age=0, must-revalidate',
            'Pragma'              => 'public'
        ]);
    }
}
