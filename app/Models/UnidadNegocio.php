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
        'codigo',
        'nombre',
        'razon_social',
        'ruc',
        'slin_id',
        'direccion',
        'cavali_girador_tipo_documento',
        'cavali_girador_documento',
        'cavali_girador_nombre',
        'cavali_girador_apellido',
        'cavali_girador_email',
        'cavali_girador_telefono',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $unidad): void {
            if (! empty($unidad->codigo)) {
                $unidad->codigo = strtoupper(trim((string) $unidad->codigo));
                return;
            }

            $unidad->codigo = static::generarCodigoSecuencial(
                (int) static::withTrashed()->max('id') + 1
            );
        });
    }

    public static function generarCodigoSecuencial(int $indice): string
    {
        $indice = max(1, $indice) - 1;

        $codigo = '';

        for ($i = 0; $i < 3; $i++) {
            $codigo = chr(65 + ($indice % 26)) . $codigo;
            $indice = intdiv($indice, 26);
        }

        return $codigo;
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

}
