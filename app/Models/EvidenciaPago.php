<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvidenciaPago extends Model
{
    /** @use HasFactory<\Database\Factories\EvidenciaPagoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'solicitud_evidencia_pago_id',
        'estado_solicitud_evidencia_pago_id',
        'path',
        'url',
        'extension',
        'numero_operacion',
        'banco',
        'monto',
        'fecha',
        'es_reenvio',
        'slin_respuesta',
        'observacion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudEvidenciaPago::class, 'solicitud_evidencia_pago_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoSolicitudEvidenciaPago::class, 'estado_solicitud_evidencia_pago_id');
    }
}
