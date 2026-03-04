<?php

namespace App\Events;

use App\Models\ProspectoEntregaFest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProspectoLegalConforme
{
    use Dispatchable, SerializesModels;

    public function __construct(public ProspectoEntregaFest $prospecto)
    {
        //
    }
}
