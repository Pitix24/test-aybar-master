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

    /**
     * El número de segundos que el trabajo puede ejecutarse antes de que se agote el tiempo de espera.
     * @var int
     */
    public $timeout = 3600;

    public function handle(CavaliService $service): void
    {
        set_time_limit(0);
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

        // Procesamos por partes para no saturar la memoria ni el tiempo en una sola consulta
        SolicitudDigitalizarLetra::where('estado_solicitud_digitalizar_letra_id', $idEstadoEnviado)
            ->chunk(10, function ($solicitudes) use ($service, $idEstadoAprobado) {
                /** @var SolicitudDigitalizarLetra $solicitud */
                foreach ($solicitudes as $solicitud) {
                    try {
                        // nroCavali = codigo_venta + numero_cuota
                        $nroCavali = ($solicitud->codigo_venta ?? '') . '-' . ($solicitud->numero_cuota ?? '');

                        if (empty($nroCavali)) {
                            Log::channel('cavali')->warning('JOB VALIDAR: Solicitud sin nroCavali', ['id' => $solicitud->id]);
                            continue;
                        }

                        $result = $service->obtenerConstanciaCancelacion($nroCavali);

                        if (($result['codigo'] ?? '') === '001' && !empty($result['base64'])) {
                            $solicitud->update(['estado_solicitud_digitalizar_letra_id' => $idEstadoAprobado]);
                            Log::channel('cavali')->info('JOB VALIDAR: Solicitud aprobada', ['id' => $solicitud->id, 'nro' => $nroCavali]);
                        }
                    } catch (\Exception $e) {
                        Log::channel('cavali')->error('JOB VALIDAR: Error en solicitud ' . $solicitud->id, ['msg' => $e->getMessage()]);
                    }
                }
                // Forzamos un pequeño respiro al servidor
                gc_collect_cycles();
            });

        Log::channel('cavali')->info('JOB VALIDAR: Proceso finalizado');
    }
}
