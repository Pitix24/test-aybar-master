<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrupoProyecto extends Model
{
    /** @use HasFactory<\Database\Factories\GrupoProyectoFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'grupo_proyectos';

    protected $fillable = [
        'nombre',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
