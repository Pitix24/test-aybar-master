<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class EntregaFest extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\EntregaFestFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia;

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
        'activo' => 'boolean',
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

    public function plantillas()
    {
        return $this->hasMany(EntregaFestPlantilla::class, 'entrega_fest_id');
    }

    /**
     * Determina si el evento ya se realizó (o se realiza HOY).
     *
     * Regla de negocio: Las invitaciones se desactivan EL DÍA del evento.
     * Es decir, el último día válido para enviar comunicaciones es
     * el día ANTERIOR a fecha_entrega.
     *
     * @return bool
     */
    public function realizado(): bool
    {
        if (!$this->fecha_entrega) {
            return false; // Defensivo: sin fecha, no bloqueamos
        }

        // fecha_entrega es DATE (Carbon ya lo castea por $casts)
        // Si la fecha del evento es HOY o anterior → bloqueamos
        return $this->fecha_entrega->startOfDay()->lte(now()->startOfDay());
    }

    /**
     * Inverso semántico, útil para legibilidad en los Listeners.
     */
    public function vigente(): bool
    {
        return !$this->realizado();
    }

}
