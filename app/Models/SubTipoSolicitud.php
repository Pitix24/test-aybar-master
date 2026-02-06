<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTipoSolicitud extends Model
{
    /** @use HasFactory<\Database\Factories\SubTipoSolicitudFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['tipo_solicitud_id', 'nombre', 'tiempo_solucion', 'activo'];

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
