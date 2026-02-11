<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoCita extends Model
{
    /** @use HasFactory<\Database\Factories\EstadoCitaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'color',
        'icono',
        'activo',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'estado_cita_id');
    }
}
