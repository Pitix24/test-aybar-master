<?php

namespace App\Events\EntregaFest;

use App\Models\EntregaFest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntregaFestAsistenciaInvitacionMasivo
{
    use Dispatchable, SerializesModels;

    public function __construct(public EntregaFest $evento)
    {
    }
}
