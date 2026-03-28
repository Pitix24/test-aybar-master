<?php

namespace App\Events\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntregaFestContratoPreliminar
{
    use Dispatchable, SerializesModels;

    public function __construct(public ProspectoEntregaFest $prospecto)
    {
        //
    }
}
