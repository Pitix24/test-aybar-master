<?php

namespace App\Models\Erp\EntregaFest;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntregaFestHistorialComunicacion extends Model
{
    use SoftDeletes;

    protected $table = 'entrega_fest_historial_comunicaciones';

    protected $fillable = [
        'persona_id',
        'persona_type',
        'canal',
        'etapa',
        'estado',
        'metadata',
        'fecha_envio'
    ];

    protected $casts = [
        'metadata' => 'array',
        'fecha_envio' => 'datetime'
    ];

    public function persona()
    {
        return $this->morphTo();
    }
}
