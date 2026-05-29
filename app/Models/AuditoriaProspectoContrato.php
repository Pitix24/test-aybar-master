<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoriaProspectoContrato extends Model
{
    use HasFactory;

    protected $table = 'auditoria_prospecto_contratos';

    protected $fillable = [
        'prospecto_entrega_fest_id',
        'user_id',
        'media_id',
        'accion',
        'collection_name',
        'file_name',
        'ip_address',
        'user_agent',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
