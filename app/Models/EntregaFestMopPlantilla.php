<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestMopPlantilla extends Model
{
    protected $table = 'entrega_fest_mop_plantillas';
    protected $fillable = ["rol_nombre", "fase", "instruccion", "prioridad"];
}
