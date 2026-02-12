<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestSchedulerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');

        Log::info('🎯 TEST SCHEDULER: Job ejecutado exitosamente', [
            'timestamp' => $timestamp,
            'message' => '¡El scheduler está funcionando correctamente!'
        ]);

        // También puedes escribir en un archivo de texto para verificar
        $logFile = storage_path('logs/scheduler_test.log');
        $content = "[{$timestamp}] ✅ Job ejecutado correctamente\n";
        file_put_contents($logFile, $content, FILE_APPEND);
    }
}
