<?php

namespace App\Services\EntregaFest\Recovery;

use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Events\EntregaFest\EntregaFestContratoPreliminar;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntregaFestN8NRecoveryService
{
    /**
     * Re-dispara el flujo de Asistencia (asistencia-confirmacion -> instrucciones).
     * Soporta IDs de InvitadoEntregaFest, ProspectoEntregaFest o Copropietario.
     */
    public function recoverAsistencia(array $ids, bool $dryRun = false, int $delaySeconds = 1): array
    {
        $stats = ['total' => count($ids), 'processed' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($ids as $id) {
            try {
                $invitado = InvitadoEntregaFest::where('id', $id)
                    ->orWhere('prospecto_entrega_fest_id', $id)
                    ->orWhere('copropietario_entrega_fest_id', $id)
                    ->first();

                if (!$invitado) {
                    Log::channel('entrega-fest')->warning("[RECOVERY-ASISTENCIA] No se encontró Invitado para el ID #{$id}.");
                    $stats['skipped']++;
                    continue;
                }

                if ($dryRun) {
                    Log::channel('entrega-fest')->info("[DRY-RUN] Se reintentaría Asistencia para Invitado #{$invitado->id} ({$invitado->nombre_completo})");
                    $stats['processed']++;
                    continue;
                }

                // Disparar evento de dominio existente (encadena asistencia-confirmacion -> instrucciones)
                EntregaFestAsistenciaConfirmacion::dispatch($invitado);

                Log::channel('entrega-fest')->info("[RECOVERY-ASISTENCIA] Evento disparado exitosamente para Invitado #{$invitado->id}");
                $stats['processed']++;

                if ($delaySeconds > 0) {
                    sleep($delaySeconds);
                }
            } catch (\Throwable $e) {
                Log::channel('entrega-fest')->error("[RECOVERY-ASISTENCIA] Error en ID #{$id}: " . $e->getMessage());
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Re-dispara el flujo de Contrato Preliminar (contrato-preliminar -> cita-agendar).
     */
    public function recoverContrato(array $ids, bool $dryRun = false, int $delaySeconds = 1): array
    {
        $stats = ['total' => count($ids), 'processed' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($ids as $id) {
            try {
                $prospecto = ProspectoEntregaFest::find($id);

                if (!$prospecto) {
                    Log::channel('entrega-fest')->warning("[RECOVERY-CONTRATO] Prospecto #{$id} no encontrado.");
                    $stats['skipped']++;
                    continue;
                }

                if ($dryRun) {
                    Log::channel('entrega-fest')->info("[DRY-RUN] Se reintentaría Contrato para Prospecto #{$prospecto->id} ({$prospecto->nombres})");
                    $stats['processed']++;
                    continue;
                }

                // Disparar evento de dominio existente (encadena contrato-preliminar -> cita-agendar)
                EntregaFestContratoPreliminar::dispatch($prospecto);

                Log::channel('entrega-fest')->info("[RECOVERY-CONTRATO] Evento disparado exitosamente para Prospecto #{$prospecto->id}");
                $stats['processed']++;

                if ($delaySeconds > 0) {
                    sleep($delaySeconds);
                }
            } catch (\Throwable $e) {
                Log::channel('entrega-fest')->error("[RECOVERY-CONTRATO] Error en Prospecto #{$id}: " . $e->getMessage());
                $stats['errors']++;
            }
        }

        return $stats;
    }

    /**
     * Método helper para ejecutar automáticamente la recuperación completa del flujo de Contrato y Cita.
     * Ideal para ser llamado directamente desde un botón en Livewire/Controller.
     */
    public function runAutoRecoverContrato(bool $dryRun = false, int $delaySeconds = 1): array
    {
        $ids = $this->getPendingContratoIds();
        return $this->recoverContrato($ids, $dryRun, $delaySeconds);
    }

    /**
     * Método helper para ejecutar automáticamente la recuperación completa del flujo de Asistencia.
     * Ideal para ser llamado directamente desde un botón en Livewire/Controller.
     */
    public function runAutoRecoverAsistencia(bool $dryRun = false, int $delaySeconds = 1): array
    {
        $ids = $this->getPendingAsistenciaIds();
        return $this->recoverAsistencia($ids, $dryRun, $delaySeconds);
    }

    /**
     * Obtiene automáticamente los IDs pendientes para Contrato y Cita mediante el Stored Procedure.
     */
    public function getPendingContratoIds(): array
    {
        $rows = DB::select("CALL sp_get_pending_entregafest_contrato_ids()");
        return array_column($rows, 'prospecto_id');
    }

    /**
     * Obtiene automáticamente los IDs de Invitados pendientes para Asistencia mediante el Stored Procedure.
     */
    public function getPendingAsistenciaIds(): array
    {
        $rows = DB::select("CALL sp_get_pending_entregafest_asistencia_ids()");
        return array_column($rows, 'invitado_id');
    }
}
