<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EstadoSolicitudEvidenciaPago extends Model
{
    /** @use HasFactory<\Database\Factories\EstadoSolicitudEvidenciaPagoFactory> */
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
        return $this->hasMany(SolicitudEvidenciaPago::class);
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciaPago::class);
    }

    public function evidenciasAntiguo()
    {
        return $this->hasMany(EvidenciaPagoAntiguo::class);
    }

    public static function id(string $nombre): int
    {
        return static::where('nombre', $nombre)->value('id');
    }
}
