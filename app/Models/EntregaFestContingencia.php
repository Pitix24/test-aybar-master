<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestContingencia extends Model
{
    protected $table = 'entrega_fest_contingencias';
    protected $fillable = ["entrega_fest_id", "escenario", "accion", "orden"];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
