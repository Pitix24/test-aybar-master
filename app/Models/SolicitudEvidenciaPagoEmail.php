<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudEvidenciaPagoEmail extends Model
{
    /** @use HasFactory<\Database\Factories\SolicitudEvidenciaPagoEmailFactory> */
    use HasFactory;

    protected $fillable = [
        'solicitud_evidencia_pago_id',
        'mensaje',
        'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudEvidenciaPago::class);
    }
}
