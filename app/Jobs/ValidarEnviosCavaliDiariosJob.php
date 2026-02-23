<?php

namespace App\Jobs;

use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Models\SolicitudDigitalizarLetra;
use App\Services\CavaliService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ValidarEnviosCavaliDiariosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(CavaliService $service): void
    {
        Log::channel('cavali')->info('JOB VALIDAR: Iniciando validación de envíos');

        $idEstadoEnviado = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::ENVIADO);
        $idEstadoAprobado = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::APROBADO);

        if (!$idEstadoEnviado || !$idEstadoAprobado) {
            Log::channel('cavali')->error('JOB VALIDAR: No se encontraron los IDs de estados necesarios', [
                'ENVIADO' => $idEstadoEnviado,
                'APROBADO' => $idEstadoAprobado,
            ]);
            return;
        }

        $solicitudes = SolicitudDigitalizarLetra::where('estado_solicitud_digitalizar_letra_id', $idEstadoEnviado)->get();

        if ($solicitudes->isEmpty()) {
            Log::channel('cavali')->info('JOB VALIDAR: No hay solicitudes con estado ENVIADO para validar');
            return;
        }

        foreach ($solicitudes as $solicitud) {
            try {
                // nroCavali = codigo_venta + numero_cuota (según lógica previa)
                $nroCavali = ($solicitud->codigo_venta ?? '') . '-' . ($solicitud->numero_cuota ?? '');

                if (empty($nroCavali)) {
                    Log::channel('cavali')->warning('JOB VALIDAR: Solicitud sin nroCavali (codigo_cuota + numero_cuota)', [
                        'id' => $solicitud->id,
                        'codigo_venta' => $solicitud->codigo_venta
                    ]);
                    continue;
                }

                $result = $service->obtenerConstanciaCancelacion($nroCavali);

                // '001' es el código de éxito según el servicio Cavali/Canvia
                if (($result['codigo'] ?? '') === '001' && !empty($result['base64'])) {
                    $solicitud->update([
                        'estado_solicitud_digitalizar_letra_id' => $idEstadoAprobado,
                        // Podríamos guardar el base64 si fuera necesario, pero el usuario no lo pidió explícitamente aquí
                    ]);

                    Log::channel('cavali')->info('JOB VALIDAR: Solicitud validada correctamente en Cavali', [
                        'id' => $solicitud->id,
                        'nroCavali' => $nroCavali,
                        'nuevo_estado' => 'APROBADO'
                    ]);
                } else {
                    Log::channel('cavali')->info('JOB VALIDAR: Solicitud aún no procesada en Cavali o con error', [
                        'id' => $solicitud->id,
                        'nroCavali' => $nroCavali,
                        'codigo_respuesta' => $result['codigo'] ?? 'N/A'
                    ]);
                }
            } catch (\Exception $e) {
                Log::channel('cavali')->error('JOB VALIDAR: Error al validar solicitud', [
                    'id' => $solicitud->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::channel('cavali')->info('JOB VALIDAR: Proceso finalizado');
    }
}
