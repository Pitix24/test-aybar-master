<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudEvidenciaMensaje extends Model
{
    /** @use HasFactory<\Database\Factories\SolicitudEvidenciaMensajeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'solicitud_evidencia_pago_id',
        'user_id',
        'mensaje',
        'es_interno',
    ];

    public function solicitudEvidenciaPago()
    {
        return $this->belongsTo(SolicitudEvidenciaPago::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
