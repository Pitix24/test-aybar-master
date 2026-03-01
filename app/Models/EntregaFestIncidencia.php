<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EntregaFestIncidencia extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'entrega_fest_incidencias';

    protected $fillable = [
        'entrega_fest_id',
        'tipo',
        'prioridad',
        'descripcion',
        'ubicacion',
        'informante_user_id',
        'responsable_user_id',
        'estado',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function informante()
    {
        return $this->belongsTo(User::class, 'informante_user_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_user_id');
    }
}
