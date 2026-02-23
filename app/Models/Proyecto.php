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

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function grupoProyecto()
    {
        return $this->belongsTo(GrupoProyecto::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function entregaFests()
    {
        return $this->belongsToMany(EntregaFest::class, 'entrega_fest_proyecto');
    }
}
