<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvanceProyecto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'avance_proyectos';

    protected $fillable = [
        'unidad_negocio_id',
        'grupo_proyecto_id',
        'proyecto_id',
        'titulo',
        'descripcion',
        'video_id',
        'clicks',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'clicks' => 'integer',
        'orden' => 'integer',
    ];

    /**
     * Relación con la Unidad de Negocio.
     */
    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    /**
     * Relación con el Grupo de Proyecto.
     */
    public function grupoProyecto()
    {
        return $this->belongsTo(GrupoProyecto::class);
    }

    /**
     * Relación con el Proyecto.
     */
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Obtener la miniatura del avance.
     */
    public function miniatura(): MorphOne
    {
        return $this->morphOne(MarketingArchivo::class, 'archivable');
    }

    /**
     * Obtener todos los archivos del avance.
     */
    public function archivos(): MorphMany
    {
        return $this->morphMany(MarketingArchivo::class, 'archivable');
    }
}
