<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyecto extends Model
{
    /** @use HasFactory<\Database\Factories\ProyectoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unidad_negocio_id',
        'grupo_proyecto_id',
        'slin_id',
        'nombre',
        'activo',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function grupoProyecto()
    {
        return $this->belongsTo(GrupoProyecto::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('activo', 'like', "%{$search}%")
                    ->orWhereHas('unidadNegocio', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('grupoProyecto', function ($query) use ($search) {
                        $query->where('nombre', 'like', "%{$search}%");
                    });
            });
        }
    }
}
