<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',

        'area_id',
        'ticket_padre_id',

        'tipo_solicitud_id',
        'sub_tipo_solicitud_id',
        'canal_id',
        'estado_ticket_id',
        'prioridad_ticket_id',
        'gestor_id',
        'asunto_inicial',
        'descripcion_inicial',
        'lotes',
        'asunto_respuesta',
        'descripcion_respuesta',

        'dni',
        'nombres',
        'email',
        'direccion',
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
        'lotes' => 'array',
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

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class, 'tipo_solicitud_id');
    }

    public function subTipoSolicitud()
    {
        return $this->belongsTo(SubTipoSolicitud::class, 'sub_tipo_solicitud_id');
    }

    public function canal()
    {
        return $this->belongsTo(Canal::class);
    }

    public function estado()
    {
        return $this->belongsTo(EstadoTicket::class, 'estado_ticket_id');
    }

    public function prioridad()
    {
        return $this->belongsTo(PrioridadTicket::class, 'prioridad_ticket_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function padre()
    {
        return $this->belongsTo(Ticket::class, 'ticket_padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(Ticket::class, 'ticket_padre_id');
    }

    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'usuario_valida_id');
    }

    public function participantes()
    {
        return $this->hasMany(TicketParticipante::class);
    }

    public function usuariosParticipantes()
    {
        return $this->belongsToMany(User::class, 'ticket_participantes')
            ->withPivot('activo')
            ->withTimestamps();
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminadoPor()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (auth()->check()) {
                $ticket->created_by = auth()->id();
            }
        });

        static::updating(function ($ticket) {
            if (auth()->check()) {
                $ticket->updated_by = auth()->id();
            }
        });

        static::deleting(function ($ticket) {
            if (auth()->check()) {
                $ticket->deleted_by = auth()->id();
                $ticket->saveQuietly();
            }
        });
    }
}
