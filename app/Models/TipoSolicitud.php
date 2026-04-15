<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoSolicitud extends Model
{
    /** @use HasFactory<\Database\Factories\TipoSolicitudFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre', 'tiempo_solucion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_tipo_solicitud');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tipo_solicitud_user')
            ->withPivot('is_principal')
            ->withTimestamps();
    }

    public function usuarioPrincipal()
    {
        return $this->belongsToMany(User::class, 'tipo_solicitud_user')
            ->withPivot('is_principal')
            ->wherePivot('is_principal', true);
    }

    public function subTipos()
    {
        return $this->hasMany(SubTipoSolicitud::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function flujoPasos()
    {
        return $this->hasMany(FlujoPaso::class)->orderBy('orden');
    }
}
