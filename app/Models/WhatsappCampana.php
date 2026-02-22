<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappCampana extends Model
{
    protected $table = 'whatsapp_campanas';
    protected $fillable = [
        'nombre',
        'plantilla_id',
        'segmento_filtro',
        'estado',
        'total_enviados',
        'total_leidos',
        'programado_para'
    ];

    protected $casts = [
        'segmento_filtro' => 'array',
        'programado_para' => 'datetime'
    ];

    public function plantilla()
    {
        return $this->belongsTo(WhatsappPlantilla::class);
    }
}
