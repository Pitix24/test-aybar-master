<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappConocimiento extends Model
{
    protected $table = 'whatsapp_conocimiento';
    protected $fillable = ['pregunta_clave', 'respuesta', 'activo'];
}
