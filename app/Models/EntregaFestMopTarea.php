<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EntregaFestMopTarea extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'entrega_fest_mop_tareas';
    protected $fillable = ["user_id", "entrega_fest_id", "titulo", "fase", "instruccion", "esta_completado", "completado_at"];

    protected $casts = [
        'completado_at' => 'datetime',
        'esta_completado' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidencias')
            ->singleFile();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
