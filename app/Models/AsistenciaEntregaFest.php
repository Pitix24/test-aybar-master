<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\AsistenciaEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'invitado_entrega_fest_id',
        'user_id',
        'fecha_checkin',
        'metodo',
    ];

    protected $casts = [
        'fecha_checkin' => 'datetime',
    ];

    public function invitado()
    {
        return $this->belongsTo(InvitadoEntregaFest::class, 'invitado_entrega_fest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
