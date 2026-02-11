<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoSolicitudDigitalizarLetra extends Model
{
    /** @use HasFactory<\Database\Factories\EstadoSolicitudDigitalizarLetraFactory> */
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

    public const PENDIENTE = 'PENDIENTE';
    public const RECHAZADO = 'RECHAZADO';
    public const APROBADO = 'APROBADO';

    public function solicitudes()
    {
        return $this->hasMany(SolicitudDigitalizarLetra::class);
    }

    public static function id(string $nombre): int
    {
        return static::where('nombre', $nombre)->value('id');
    }
}
