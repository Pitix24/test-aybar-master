<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitadoEnvioEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\InvitadoEnvioEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'invitado_entrega_fest_id',
        'canal',
        'estado',
        'detalle',
        'user_id',
        'fecha_envio',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    public function invitado()
    {
        return $this->belongsTo(InvitadoEntregaFest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
