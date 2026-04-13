<?php

namespace App\Models\LibroReclamacion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoLibroReclamacion extends Model
{
    protected $table = 'estado_libro_reclamaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'es_final',
        'orden',
    ];

    protected $casts = [
        'es_final' => 'boolean',
    ];

    /**
     * Obtener todas las reclamaciones con este estado.
     */
    public function reclamaciones(): HasMany
    {
        return $this->hasMany(LibroReclamacion::class, 'estado_libro_reclamaciones_id');
    }
}
