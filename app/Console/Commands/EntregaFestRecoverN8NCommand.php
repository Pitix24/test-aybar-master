<?php

namespace App\Console\Commands;

use App\Services\EntregaFest\Recovery\EntregaFestN8NRecoveryService;
use Illuminate\Console\Command;

class EntregaFestRecoverN8NCommand extends Command
{
    /**
     * Formas de uso:
     * 1. Modo automático (Corre la query sola en la BD):
     *    php artisan entregafest:recover-n8n --flow=contrato --auto
     *    php artisan entregafest:recover-n8n --flow=asistencia --auto
     * 
     * 2. Modo manual (Ingresando IDs a mano):
     *    php artisan entregafest:recover-n8n --flow=contrato --ids=101,102
     * 
     * 3. Simulación (Sin envíos reales a n8n):
     *    php artisan entregafest:recover-n8n --flow=contrato --auto --dry-run
     */
    protected $signature = 'entregafest:recover-n8n 
                            {--flow= : El flujo a reintentar (asistencia|contrato)}
                            {--ids= : IDs separados por coma (ej. 101,102,103)}
                            {--auto : Ejecuta automáticamente la query SQL para obtener todos los IDs pendientes}
                            {--dry-run : Modo simulación sin realizar llamadas HTTP reales}
                            {--delay=1 : Segundos de espera entre cada envío}';

    protected $description = 'Re-dispara flujos de n8n no procesados por caídas del servicio';

    public function handle(EntregaFestN8NRecoveryService $recoveryService): int
    {
        $flow = $this->option('flow');
        $rawIds = $this->option('ids');
        $auto = (bool) $this->option('auto');
        $dryRun = (bool) $this->option('dry-run');
        $delay = (int) $this->option('delay');

        if (!in_array($flow, ['asistencia', 'contrato'])) {
            $this->error('Opción --flow inválida. Debe especificar --flow=asistencia o --flow=contrato');
            return Command::FAILURE;
        }

        // Determinación de los IDs a procesar
        if ($auto) {
            $this->info("Ejecutando consulta SQL automática para flujo [{$flow}]...");
            $ids = ($flow === 'asistencia') 
                ? $recoveryService->getPendingAsistenciaIds() 
                : $recoveryService->getPendingContratoIds();
        } else {
            if (empty($rawIds)) {
                $this->error('Debe ingresar los IDs a procesar mediante --ids=1,2,3 o usar la opción --auto');
                return Command::FAILURE;
            }
            $ids = array_filter(array_map('trim', explode(',', $rawIds)));
        }

        if (empty($ids)) {
            $this->info("No se encontraron registros pendientes para el flujo [{$flow}].");
            return Command::SUCCESS;
        }

        $this->info("Iniciando recuperación para flujo [{$flow}] - Cantidad de IDs a procesar: " . count($ids));
        if ($dryRun) {
            $this->warn('*** MODO DRY-RUN ACTIVADO (No se realizarán llamadas reales a n8n) ***');
        }

        $results = ($flow === 'asistencia')
            ? $recoveryService->recoverAsistencia($ids, $dryRun, $delay)
            : $recoveryService->recoverContrato($ids, $dryRun, $delay);

        $this->table(
            ['Total IDs', 'Procesados', 'Ignorados/No Encontrados', 'Errores'],
            [[$results['total'], $results['processed'], $results['skipped'], $results['errors']]]
        );

        $this->info('Proceso de recuperación finalizado.');
        return Command::SUCCESS;
    }
}
