<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestItinerarioBloque extends Model
{
    protected $table = 'entrega_fest_itinerario_bloques';
    protected $fillable = ["entrega_fest_id", "hora_inicio", "hora_fin", "titulo", "descripcion", "ubicacion", "responsable_rol", "estado", "orden"];

    public function checklists()
    {
        return $this->hasMany(EntregaFestItinerarioChecklist::class, "itinerario_bloque_id");
    }

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
