<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\AreaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'email_buzon',
        'color',
        'icono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_principal')
            ->withTimestamps();
    }

    public function sedes()
    {
        return $this->belongsToMany(Sede::class)->withTimestamps();
    }

    public function principal()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_principal')
            ->wherePivot('is_principal', true);
    }

    public function tiposSolicitud()
    {
        return $this->belongsToMany(TipoSolicitud::class, 'area_tipo_solicitud')->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
