<?php

namespace App\Http\Controllers\Erp;

use Illuminate\Http\Request;
use App\Models\ProspectoEntregaFest;
use App\Models\CopropietarioEntregaFest;
use App\Http\Controllers\Controller;

class ProspectoEntregaFestController extends Controller
{
    public function marcarEnviado(Request $request)
    {
        $id = $request->id;
        $tipo = $request->tipo; // 'Propietario' o 'Copropietario'

        if ($tipo === 'Propietario') {
            ProspectoEntregaFest::where('id', $id)->update(['enviado_preinvitacion' => true]);
        } elseif ($tipo === 'Copropietario') {
            CopropietarioEntregaFest::where('id', $id)->update(['enviado_preinvitacion' => true]);
        }

        return response()->json(['message' => 'Status actualizado correctamente']);
    }
}
