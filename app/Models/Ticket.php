<?php

namespace App\Models;

use App\Models\LibroReclamacion\LibroReclamacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'celular',
        'direccion',
        'origen',
        'usuario_valida_id',
        'fecha_validacion',
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

    public function scopeCartasNotariales($query)
    {
        $tipoSolicitudIds = static::cartasNotarialesTipoSolicitudIds();

        if (empty($tipoSolicitudIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('tipo_solicitud_id', $tipoSolicitudIds);
    }

    public function scopeSinCartasNotariales($query)
    {
        $tipoSolicitudIds = static::cartasNotarialesTipoSolicitudIds();

        if (empty($tipoSolicitudIds)) {
            return $query;
        }

        return $query->where(function ($subQuery) use ($tipoSolicitudIds) {
            $subQuery->whereNull('tipo_solicitud_id')
                ->orWhereNotIn('tipo_solicitud_id', $tipoSolicitudIds);
        });
    }

    public function esCartaNotarial(): bool
    {
        $tipoSolicitud = TipoSolicitud::withTrashed()->find($this->tipo_solicitud_id)?->nombre;

        if (!is_string($tipoSolicitud)) {
            return false;
        }

        $tipoNormalizado = mb_strtoupper(trim($tipoSolicitud));

        return str_contains($tipoNormalizado, 'NOTARIAL');
    }

    protected static function cartasNotarialesTipoSolicitudIds(): array
    {
        return TipoSolicitud::withTrashed()
            ->whereRaw('UPPER(nombre) LIKE ?', ['%NOTARIAL%'])
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function getTieneDerivadosAttribute()
    {
        return $this->derivados()->exists();
    }

    public function getTieneArchivosAttribute()
    {
        return $this->archivos()->exists();
    }

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (Auth::check()) {
                $ticket->created_by = Auth::id();
            }
        });

        static::updating(function ($ticket) {
            if (Auth::check()) {
                $ticket->updated_by = Auth::id();
            }
        });

        static::deleting(function ($ticket) {
            if (Auth::check()) {
                $ticket->deleted_by = Auth::id();
                $ticket->estado_ticket_id = 7;
                $ticket->saveQuietly();
            }
        });
    }
}
