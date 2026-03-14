<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcompananteEntregaFest extends Model
{
    protected $fillable = [
        'dni',
        'nombres',
        'email',
        'celular',
        'prospecto_entrega_fest_id',
        'invitado_entrega_fest_id',
    ];

    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function invitado()
    {
        return $this->belongsTo(InvitadoEntregaFest::class, 'invitado_entrega_fest_id');
    }
}
