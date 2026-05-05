<?php

namespace App\Models;

use App\Models\LibroReclamacion\LibroReclamacion;
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
        'region_id',
        'provincia_id',
        'distrito_id',
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

    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = $value !== null ? strtoupper(trim((string) $value)) : null;
    }

    public function proyectos()
    {
        return $this->hasMany(Proyecto::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function avances()
    {
        return $this->hasMany(AvanceProyecto::class);
    }

    public function libroReclamacions()
    {
        return $this->hasMany(LibroReclamacion::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }
}
