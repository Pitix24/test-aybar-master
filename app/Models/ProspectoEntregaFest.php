<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectoEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\ProspectoEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'entrega_fest_id',
        'user_id',
        'dni',
        'nombre',
        'apellidos',
        'estado',
        'observacion',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function invitado()
    {
        return $this->hasOne(InvitadoEntregaFest::class);
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
