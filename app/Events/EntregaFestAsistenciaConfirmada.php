<?php

namespace App\Events;

use App\Models\InvitadoEntregaFest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntregaFestAsistenciaConfirmada
{
    use Dispatchable, SerializesModels;

    public function __construct(public InvitadoEntregaFest $invitado)
    {
        //
    }
}
