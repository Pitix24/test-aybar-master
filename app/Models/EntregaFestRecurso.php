<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EntregaFestRecurso extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'entrega_fest_recursos';

    protected $fillable = [
        'entrega_fest_id',
        'nombre_publico',
        'tipo_recurso',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
