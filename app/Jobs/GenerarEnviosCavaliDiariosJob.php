<?php

namespace App\Jobs;

use App\Models\EnvioCavali;
use App\Models\SolicitudDigitalizarLetra;
use App\Models\UnidadNegocio;
use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Exports\CavaliExport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerarEnviosCavaliDiariosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $fecha = now()->toDateString();
        $idEstadoPendiente = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::PENDIENTE);
        $idEstadoEnviado = EstadoSolicitudDigitalizarLetra::id(EstadoSolicitudDigitalizarLetra::ENVIADO);

        UnidadNegocio::query()->each(function ($unidad) use ($fecha, $idEstadoPendiente, $idEstadoEnviado) {
            DB::beginTransaction();
            try {
                $solicitudes = SolicitudDigitalizarLetra::where('unidad_negocio_id', $unidad->id)
                    ->where('estado_solicitud_digitalizar_letra_id', $idEstadoPendiente)
                    ->get();

                if ($solicitudes->isEmpty()) {
                    DB::rollBack();
                    Log::channel('cavali')->info('JOB CAVALI: No hay solicitudes pendientes', [
                        'unidad_negocio_id' => $unidad->id,
                        'razon_social' => $unidad->razon_social,
                    ]);
                    return;
                }

                // Evita duplicar cortes
                $envio = EnvioCavali::firstOrCreate(
                    [
                        'fecha_corte' => $fecha,
                        'unidad_negocio_id' => $unidad->id,
                    ],
                    [
                        'estado_solicitud_digitalizar_letra_id' => $idEstadoPendiente,
                    ]
                );

                $result = $envio->solicitudes()->syncWithoutDetaching(
                    $solicitudes->pluck('id')
                );

                Log::channel('cavali')->info('JOB CAVALI: sync ejecutado', [
                    'envio_id' => $envio->id,
                    'attached' => $result['attached'] ?? [],
                    'updated' => $result['updated'] ?? [],
                ]);

                // Sanitizar nombre de archivo (remover caracteres especiales)
                $razonSocialSanitizada = preg_replace('/[^A-Za-z0-9_\-]/', '_', $unidad->razon_social);
                $fileName = "CAVALI_{$razonSocialSanitizada}_{$fecha}.xlsx";
                $path = "cavali/{$fecha}/{$fileName}";

                // Generar Excel
                Excel::store(
                    new CavaliExport($envio),
                    $path,
                    'local'
                );

                // Verificar que el archivo se generó correctamente
                if (!Storage::disk('local')->exists($path)) {
                    throw new \Exception("No se pudo generar el archivo Excel en: {$path}");
                }

                $fileSize = Storage::disk('local')->size($path);
                Log::channel('cavali')->info('JOB CAVALI: excel generado exitosamente', [
                    'path' => storage_path('app/' . $path),
                    'size' => $fileSize . ' bytes',
                    'solicitudes_count' => $solicitudes->count(),
                ]);

                // Actualizar envío
                $envio->update([
                    'estado_solicitud_digitalizar_letra_id' => $idEstadoEnviado,
                    'enviado_at' => now(),
                    'archivo_zip' => $path,
                ]);

                // Actualizar solicitudes
                SolicitudDigitalizarLetra::whereIn('id', $solicitudes->pluck('id'))
                    ->update(['estado_solicitud_digitalizar_letra_id' => $idEstadoEnviado]);

                // Enviar correo
                Mail::raw(
                    "Estimados, se hace llegar la base de letras pagadas físicas a desmaterializar. {$fecha}\n\nEmpresa: {$unidad->razon_social}\nTotal de solicitudes: {$solicitudes->count()}",
                    function ($message) use ($path, $fileName, $razonSocialSanitizada) {
                        $message->to('PROGRAMADOR@aybarsac.com')
                            ->cc(['mersmith14@gmail.com'])
                            ->subject("Letras físicas pagadas - {$razonSocialSanitizada}")
                            ->attach(Storage::path($path), [
                                'as' => $fileName,
                            ]);
                    }
                );

                DB::commit();

                Log::channel('cavali')->info('JOB CAVALI: Proceso completado exitosamente', [
                    'envio_id' => $envio->id,
                    'unidad_negocio' => $unidad->razon_social,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                Log::channel('cavali')->error('JOB CAVALI: Error al procesar envío', [
                    'unidad_negocio_id' => $unidad->id,
                    'razon_social' => $unidad->razon_social,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });
    }
}
