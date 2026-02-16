<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitadoAcompananteEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\InvitadoAcompananteEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'invitado_entrega_fest_id',
        'dni',
        'nombre',
        'apellidos',
        'asistio',
    ];

    protected $casts = [
        'asistio' => 'boolean',
    ];

    public function invitado()
    {
        return $this->belongsTo(InvitadoEntregaFest::class);
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
