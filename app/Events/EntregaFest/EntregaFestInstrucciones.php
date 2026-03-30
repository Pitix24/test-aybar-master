<?php

namespace App\Events\EntregaFest;

use App\Models\InvitadoEntregaFest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntregaFestInstrucciones
{
    use Dispatchable, SerializesModels;

    public function __construct(public InvitadoEntregaFest $invitado)
    {
        //
    }
}
