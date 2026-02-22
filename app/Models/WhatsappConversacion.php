<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConversacion extends Model
{
    protected $table = 'whatsapp_conversaciones';
    protected $fillable = [
        'contacto_id',
        'cliente_id',
        'agente_id',
        'estado',
        'departamento_destino',
        'mensajes_sin_leer',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime'
    ];

    public function contacto()
    {
        return $this->belongsTo(WhatsappContacto::class, 'contacto_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function agente()
    {
        return $this->belongsTo(User::class, 'agente_id');
    }
    public function mensajes()
    {
        return $this->hasMany(WhatsappMensaje::class, 'conversacion_id');
    }
}
