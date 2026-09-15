<?php

namespace App\Jobs\EntregaFest;

use App\Services\EntregaFest\Recovery\EntregaFestN8NRecoveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecoverEntregaFestContratoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $prospectoId)
    {
    }

    public function handle(EntregaFestN8NRecoveryService $recoveryService): void
    {
        $recoveryService->recoverContrato([$this->prospectoId], dryRun: false, delaySeconds: 0);
    }
}
