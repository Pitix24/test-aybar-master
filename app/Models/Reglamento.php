<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reglamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reglamentos';

    protected $fillable = [
        'proyecto_id',
        'titulo',
        'descripcion',
        'clicks',
        'activo',
        'orden',
    ];

    protected $casts = [
        'proyecto_id' => 'integer',
        'clicks' => 'integer',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Relación con el proyecto.
     */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    /**
     * Obtener el archivo PDF principal del reglamento.
     */
    public function archivoPdf(): MorphOne
    {
        return $this->morphOne(MarketingArchivo::class, 'archivable');
    }

    /**
     * Obtener todos los archivos del reglamento.
     */
    public function archivos(): MorphMany
    {
        return $this->morphMany(MarketingArchivo::class, 'archivable');
    }
}
