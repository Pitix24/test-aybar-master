<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestItinerarioChecklist extends Model
{
    protected $table = 'entrega_fest_itinerario_checklists';
    protected $fillable = ["itinerario_bloque_id", "tarea", "esta_listo", "completado_at", "completado_por_user_id"];

    public function bloque()
    {
        return $this->belongsTo(EntregaFestItinerarioBloque::class, "itinerario_bloque_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "completado_por_user_id");
    }
}
