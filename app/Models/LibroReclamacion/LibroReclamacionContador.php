<?php

namespace App\Models\LibroReclamacion;

use App\Models\UnidadNegocio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibroReclamacionContador extends Model
{
    use HasFactory;

    protected $table = 'libro_reclamacion_contadores';

    protected $fillable = [
        'unidad_negocio_id',
        'siguiente_numero',
    ];

    protected $casts = [
        'siguiente_numero' => 'integer',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }
}