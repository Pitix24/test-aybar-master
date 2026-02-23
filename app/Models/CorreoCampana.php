<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorreoCampana extends Model
{
    use HasFactory;

    protected $table = 'correo_campanas';

    protected $fillable = [
        'plantilla_id',
        'lista_id',
        'nombre',
        'estado',
        'total_enviados',
        'total_errores',
        'scheduled_at',
    ];

    public function plantilla()
    {
        return $this->belongsTo(CorreoPlantilla::class, 'plantilla_id');
    }

    public function lista()
    {
        return $this->belongsTo(CorreoLista::class, 'lista_id');
    }

    public function envios()
    {
        return $this->hasMany(CorreoCampanaEnvio::class, 'campana_id');
    }
}
