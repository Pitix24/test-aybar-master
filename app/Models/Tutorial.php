<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutorial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tutoriales';

    protected $fillable = [
        'titulo',
        'descripcion',
        'video_id',
        'clicks',
        'activo',
        'orden',
    ];

    /**
     * Obtener la miniatura del tutorial.
     */
    public function miniatura(): MorphOne
    {
        return $this->morphOne(MarketingArchivo::class, 'archivable');
    }

    /**
     * Obtener todos los archivos del tutorial (si tuviera más).
     */
    public function archivos(): MorphMany
    {
        return $this->morphMany(MarketingArchivo::class, 'archivable');
    }
}
