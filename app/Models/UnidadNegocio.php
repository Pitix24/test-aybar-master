<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnidadNegocio extends Model
{
    /** @use HasFactory<\Database\Factories\UnidadNegocioFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $table = 'unidad_negocios';

    protected $fillable = [
        'nombre',
        'razon_social',
        'ruc',
        'slin_id',
        'cavali_girador_tipo_documento',
        'cavali_girador_documento',
        'cavali_girador_nombre',
        'cavali_girador_apellido',
        'cavali_girador_email',
        'cavali_girador_telefono',
    ];

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('nombre', 'like', "%{$search}%")
                ->orWhere('razon_social', 'like', "%{$search}%")
                ->orWhere('ruc', 'like', "%{$search}%")
                ->orWhere('slin_id', 'like', "%{$search}%")
                ->orWhere('cavali_girador_tipo_documento', 'like', "%{$search}%")
                ->orWhere('cavali_girador_documento', 'like', "%{$search}%")
                ->orWhere('cavali_girador_nombre', 'like', "%{$search}%")
                ->orWhere('cavali_girador_apellido', 'like', "%{$search}%")
                ->orWhere('cavali_girador_email', 'like', "%{$search}%")
                ->orWhere('cavali_girador_telefono', 'like', "%{$search}%");
        }
    }
}
