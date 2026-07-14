<?php

namespace App\Support;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;

trait RedirigeSiAforoLleno
{
    public function redirigirSiLleno(EntregaFest $evento)
    {
        // 1. Obtenemos el límite del evento de manera dinámica (con fallback seguro por si acaso)
        $limiteDinamico = $evento->limite_invitados ?? 250;

        // 2. Contamos los invitados confirmados actuales
        $totalConfirmados = InvitadoEntregaFest::where('entrega_fest_id', $evento->id)
            ->where('confirmado', true)
            ->count();

        // 3. Evaluamos contra el límite de la base de datos
        if ($totalConfirmados >= $limiteDinamico) {
            return redirect()->route('entrega-fest.aforo-lleno', ['slug' => $evento->slug]);
        }

        return null;
    }
}
