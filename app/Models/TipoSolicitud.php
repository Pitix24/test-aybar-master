<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoSolicitud extends Model
{
    /** @use HasFactory<\Database\Factories\TipoSolicitudFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre', 'tiempo_solucion', 'activo'];

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_tipo_solicitud');
    }

    public function subTipos()
    {
        return $this->hasMany(SubTipoSolicitud::class);
    }
}
