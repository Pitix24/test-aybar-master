<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitadoEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\InvitadoEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'entrega_fest_id',
        'prospecto_entrega_fest_id',
        'codigo_invitado',
        'cantidad_acompanantes_permitidos',
        'confirmado',
        'estado_confirmacion',
        'transporte',
        'observaciones_asistencia',
    ];

    protected $casts = [
        'confirmado' => 'boolean',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }


    public function asistencia()
    {
        return $this->hasOne(AsistenciaEntregaFest::class, 'invitado_entrega_fest_id');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->prospecto?->nombres;
    }
}
