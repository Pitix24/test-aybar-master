<?php

namespace App\Support;

use App\Models\EntregaFest;
use Illuminate\Support\Facades\Log;

trait VerificaEventoVigente
{
    /**
     * Verifica si el evento sigue vigente (aún no se realizó).
     * Si ya se realizó, registra el bloqueo en el log y retorna FALSE.
     *
     * @param  \App\Models\EntregaFest|null  $evento
     * @param  string  $contexto  Etiqueta para identificar el Listener en los logs
     * @return bool
     */
    protected function eventoVigente(?EntregaFest $evento, string $contexto = 'N8N'): bool
    {
        if (!$evento) {
            Log::channel('entrega-fest')->warning(
                "[{$contexto}] ❌ Evento nulo. Se aborta envío a n8n."
            );
            return false;
        }

        if ($evento->realizado()) {
            Log::channel('entrega-fest')->warning(
                "[{$contexto}] 🛑 Envío BLOQUEADO. Evento #{$evento->id} ('{$evento->nombre}') " .
                "ya fue realizado. fecha_entrega: {$evento->fecha_entrega->format('Y-m-d')}. " .
                "Se previene envío accidental a n8n."
            );
            return false;
        }

        return true;
    }
}
