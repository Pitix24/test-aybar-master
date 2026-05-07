<?php

namespace App\Models\Erp\Soporte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoSoporte extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'color',
        'icono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
