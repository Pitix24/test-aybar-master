<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EntregaFestPlantilla extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'entrega_fest_id',
        'tipo',
        'titulo',
        'subtitulo',
        'descripcion',
        'link_boton',
        'activo',
        'imagen_url' // Added 'imagen_url' to fillable
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
