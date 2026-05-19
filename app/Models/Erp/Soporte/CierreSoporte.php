<?php

namespace App\Models\Erp\Soporte;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CierreSoporte extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cierre_soportes';

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
