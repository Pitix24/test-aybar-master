<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EntregaFestItinerarioChecklist extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'entrega_fest_itinerario_checklists';
    protected $fillable = ["itinerario_bloque_id", "tarea", "esta_listo", "completado_at", "completado_por_user_id"];

    protected $casts = [
        'completado_at' => 'datetime',
        'esta_listo' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidencias')
            ->singleFile();
    }

    public function bloque()
    {
        return $this->belongsTo(EntregaFestItinerarioBloque::class, "itinerario_bloque_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "completado_por_user_id");
    }
}
