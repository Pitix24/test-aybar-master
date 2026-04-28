<?php

namespace App\Models;

use App\Models\LibroReclamacion\LibroReclamacion;
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
        'fecha_vencimiento',

        //DB ANTIGUO
        'dni',
        'nombres',
        'email',
        'celular',
        'direccion',
        'origen',

        //SUPERVISOR
        'usuario_valida_id',
        'fecha_validacion',

        //AUDITORIA
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
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

    public function userCliente()
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

    public function archivos()
    {
        return $this->morphMany(TicketArchivo::class, 'archivable');
    }

    public function historial()
    {
        return $this->hasMany(TicketHistorial::class);
    }

    public function derivados()
    {
        return $this->hasMany(TicketDerivado::class);
    }

    public function padre()
    {
        return $this->belongsTo(Ticket::class, 'ticket_padre_id');
    }

    public function hijos()
    {
        return $this->hasMany(Ticket::class, 'ticket_padre_id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function libroReclamacion()
    {
        return $this->hasOne(LibroReclamacion::class, 'ticket_id');
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

    public function mensajes()
    {
        return $this->hasMany(TicketMensaje::class);
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

    public function pasos()
    {
        return $this->hasMany(TicketPaso::class);
    }

    public function getTieneDerivadosAttribute()
    {
        return $this->derivados()->exists();
    }

    public function getTieneArchivosAttribute()
    {
        return $this->archivos()->exists();
    }

    public function getSlaStatusAttribute()
    {
        if (!$this->fecha_vencimiento || $this->estado_ticket_id == 4) { // 4 assumed as 'Cerrado'
            return null;
        }

        $ahora = now();
        $vencido = $ahora->gt($this->fecha_vencimiento);
        $diferencia = $ahora->diffForHumans($this->fecha_vencimiento, [
            'parts' => 2,
            'short' => true,
            'join' => true,
        ]);

        if ($vencido) {
            return [
                'texto' => "Vencido hace $diferencia",
                'color' => '#ef4444', // Rojo
                'clase' => 'danger'
            ];
        }

        // Si falta menos de 4 horas, poner en naranja
        $horasRestantes = $ahora->diffInHours($this->fecha_vencimiento);
        if ($horasRestantes <= 4) {
            return [
                'texto' => "Vence en $diferencia",
                'color' => '#f59e0b', // Naranja
                'clase' => 'warning'
            ];
        }

        return [
            'texto' => "Vence en $diferencia",
            'color' => '#10b981', // Verde
            'clase' => 'success'
        ];
    }

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (auth()->check()) {
                $ticket->created_by = auth()->id();
            }

            // Calcular fecha de vencimiento solo si no se ha definido una manualmente
            if (!$ticket->fecha_vencimiento && $ticket->tipo_solicitud_id) {
                $tipoSolicitud = \App\Models\TipoSolicitud::find($ticket->tipo_solicitud_id);
                if ($tipoSolicitud && $tipoSolicitud->tiempo_solucion) {
                    $ticket->fecha_vencimiento = \App\Services\TicketService::calcularFechaVencimiento(
                        now(),
                        $tipoSolicitud->tiempo_solucion
                    );
                }
            }
        });

        static::updating(function ($ticket) {
            if (auth()->check()) {
                $ticket->updated_by = auth()->id();
            }
        });

        static::updated(function ($ticket) {
            // Notificar al creador del ticket si existe
            if ($ticket->creadoPor && $ticket->created_by !== auth()->id()) {
                $ticket->creadoPor->notify(new \App\Notifications\TicketActualizadoNotification($ticket));
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
