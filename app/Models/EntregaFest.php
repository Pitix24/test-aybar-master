<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\EntregaFestFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gestor_id',
        'nombre',
        'slug',
        'descripcion',
        'codigo',
        'fecha_entrega',
        'activo',
    ];

    protected static function booted()
    {
        static::creating(function ($evento) {
            $evento->slug = \Illuminate\Support\Str::slug($evento->nombre) . '-' . uniqid();
        });
    }

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'entrega_fest_proyecto');
    }

    public function prospectos()
    {
        return $this->hasMany(ProspectoEntregaFest::class);
    }

    public function invitados()
    {
        return $this->hasMany(InvitadoEntregaFest::class);
    }

    // --- Módulo Staff ---

    public function itinerarioBloques()
    {
        return $this->hasMany(EntregaFestItinerarioBloque::class, 'entrega_fest_id')->orderBy('orden');
    }

    public function mopTareas()
    {
        return $this->hasMany(EntregaFestMopTarea::class, 'entrega_fest_id');
    }

    public function proveedores()
    {
        return $this->hasMany(EntregaFestProveedor::class, 'entrega_fest_id');
    }

    public function incidencias()
    {
        return $this->hasMany(EntregaFestIncidencia::class, 'entrega_fest_id');
    }

    public function recursos()
    {
        return $this->hasMany(EntregaFestRecurso::class, 'entrega_fest_id');
    }

    public function protocolos()
    {
        return $this->hasMany(EntregaFestProtocolo::class, 'entrega_fest_id')->orderBy('orden');
    }

    public function contingencias()
    {
        return $this->hasMany(EntregaFestContingencia::class, 'entrega_fest_id')->orderBy('orden');
    }
}
