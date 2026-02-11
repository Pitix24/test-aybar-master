<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    /** @use HasFactory<\Database\Factories\CitaFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',

        'area_id',
        'ticket_id',

        'usuario_crea_id',
        'gestor_id',
        'sede_id',
        'motivo_cita_id',
        'estado_cita_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_cierre',
        'asunto_solicitud',
        'descripcion_solicitud',
        'asunto_respuesta',
        'descripcion_respuesta',

        'dni',
        'nombres',
        'origen',

        // valida
        'usuario_valida_id',
        'fecha_validacion',

        // auditoría
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function crea()
    {
        return $this->belongsTo(User::class, 'usuario_crea_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function motivo()
    {
        return $this->belongsTo(MotivoCita::class, 'motivo_cita_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoCita::class, 'estado_cita_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // valida
    public function usuarioValida()
    {
        return $this->belongsTo(User::class, 'usuario_valida_id');
    }

    // auditoria
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminador()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted()
    {
        static::creating(function ($cita) {
            if (auth()->check()) {
                $cita->created_by = auth()->id();
            }
        });

        static::updating(function ($cita) {
            if (auth()->check()) {
                $cita->updated_by = auth()->id();
            }
        });

        static::deleting(function ($cita) {
            if (auth()->check()) {
                $cita->deleted_by = auth()->id();
                $cita->saveQuietly();
            }
        });
    }
}
