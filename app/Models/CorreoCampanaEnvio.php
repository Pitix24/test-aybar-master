<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorreoCampanaEnvio extends Model
{
    use HasFactory;

    protected $table = 'correo_campana_envios';

    protected $fillable = [
        'campana_id',
        'contacto_id',
        'estado',
        'error_mensaje',
        'enviado_at',
    ];

    public function campana()
    {
        return $this->belongsTo(CorreoCampana::class, 'campana_id');
    }

    public function contacto()
    {
        return $this->belongsTo(CorreoContacto::class, 'contacto_id');
    }
}
