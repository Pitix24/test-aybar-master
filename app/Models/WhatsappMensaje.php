<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMensaje extends Model
{
    protected $fillable = [
        'conversacion_id',
        'direccion',
        'tipo',
        'contenido',
        'wa_message_id',
        'estado',
        'reaccion'
    ];

    public function conversacion()
    {
        return $this->belongsTo(WhatsappConversacion::class);
    }
}
