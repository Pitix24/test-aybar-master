<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestMopTarea extends Model
{
    protected $table = 'entrega_fest_mop_tareas';
    protected $fillable = ["user_id", "entrega_fest_id", "titulo", "fase", "instruccion", "esta_completado", "completado_at"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
