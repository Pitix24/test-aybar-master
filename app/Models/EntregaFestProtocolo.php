<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaFestProtocolo extends Model
{
    protected $table = 'entrega_fest_protocolos';
    protected $fillable = ["entrega_fest_id", "titulo", "contenido", "orden"];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }
}
