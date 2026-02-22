<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappContacto extends Model
{
    protected $table = 'whatsapp_contactos';
    protected $fillable = [
        'wa_id',
        'nombre_wa',
        'numero_celular',
        'cliente_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function conversaciones()
    {
        return $this->hasMany(WhatsappConversacion::class, 'contacto_id');
    }
}
