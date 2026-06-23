<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProspectoEntregaFest extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProspectoEntregaFestFactory> */
    use HasFactory, InteractsWithMedia;

    // ---------------------------------------------------------------
    // ENUMS: Grupo
    // ---------------------------------------------------------------
    const GRUPOS = ['A', 'B', 'C', 'D'];

    // ---------------------------------------------------------------
    // ENUMS: Estado BackOffice
    // ---------------------------------------------------------------
    const ESTADO_BACKOFFICE = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280', 'condicion' => 'VISITANTE', 'mensaje' => 'Cliente con información de pago aún por validar.'],
        'BANCARIZAR' => ['label' => 'Bancarizar', 'color' => '#3B82F6', 'condicion' => 'ENTREGA', 'mensaje' => 'Cliente que ha cancelado, pero falta sustento (vouchers).'],
        'PENALIDAD' => ['label' => 'Penalidad', 'color' => '#EF4444', 'condicion' => 'VISITANTE', 'mensaje' => 'Cliente con deuda (moras).'],
        'OBSERVADO' => ['label' => 'Observado', 'color' => '#F59E0B', 'condicion' => 'VISITANTE', 'mensaje' => 'Cliente con cuotas pendientes.'],
        'CONFORME' => ['label' => 'Conforme', 'color' => '#10B981', 'condicion' => 'ENTREGA', 'mensaje' => 'Cliente que ha cancelado al 100%.'],
        'VIGENTE' => ['label' => 'Vigente', 'color' => '#f65a3bff', 'condicion' => 'VISITANTE', 'mensaje' => 'Cliente con pagos en proceso (cuotas al día o en curso).'],
    ];

    const ESTADO_GESTOR_BACKOFFICE = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'BANCARIZAR' => ['label' => 'Bancarizar', 'color' => '#3B82F6'],
        'PENALIDAD' => ['label' => 'Penalidad', 'color' => '#EF4444'],
        'OBSERVADO' => ['label' => 'Observado', 'color' => '#F59E0B'],
        'CONFORME' => ['label' => 'Conforme', 'color' => '#10B981'],
        'VIGENTE' => ['label' => 'Vigente', 'color' => '#f65a3bff'],
    ];

    // ---------------------------------------------------------------
    // ENUMS: Estado Contrato Preliminar
    // ---------------------------------------------------------------
    const ESTADO_CONTRATO_PRELIMINAR = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'GENERADO' => ['label' => 'Generado', 'color' => '#3B82F6'],
        'OBSERVADO' => ['label' => 'Observado', 'color' => '#F59E0B'],
        'CONFORME' => ['label' => 'Conforme', 'color' => '#10B981'],
    ];

    // ---------------------------------------------------------------
    // ENUMS: Estado Firma Contrato
    // ---------------------------------------------------------------
    const ESTADO_FIRMA = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'FIRMADO' => ['label' => 'Firmado', 'color' => '#10B981'],
    ];

    // ---------------------------------------------------------------
    // Helpers: badge color
    // ---------------------------------------------------------------
    public function badgeBackoffice(): string
    {
        return self::ESTADO_BACKOFFICE[$this->estado_backoffice]['color'] ?? '#000000';
    }

    public function badgeGestorBackoffice(): string
    {
        return self::ESTADO_GESTOR_BACKOFFICE[$this->estado_gestor_backoffice]['color'] ?? '#000000';
    }

    public function badgeContratoPreeliminar(): string
    {
        return self::ESTADO_CONTRATO_PRELIMINAR[$this->estado_contrato_preeliminar_emitido]['color'] ?? '#000000';
    }

    public function badgeFirma(): string
    {
        return self::ESTADO_FIRMA[$this->estado_firma_contrato_firmado]['color'] ?? '#000000';
    }

    public function scopeFiltrado($query, array $f)
    {
        return $query
            ->where('entrega_fest_id', $f['evento_id'])

            ->when($f['buscar'] ?? null, function ($q) use ($f) {
                $q->where(function ($sub) use ($f) {
                    $sub->where('nombres', 'like', "%{$f['buscar']}%")
                        ->orWhere('dni', 'like', "%{$f['buscar']}%")
                        ->orWhere('email', 'like', "%{$f['buscar']}%")
                        ->orWhere('celular', 'like', "%{$f['buscar']}%")
                        ->orWhereHas('copropietarios', function ($cop) use ($f) {
                            $cop->where('nombres', 'like', "%{$f['buscar']}%")
                                ->orWhere('dni', 'like', "%{$f['buscar']}%")
                                ->orWhere('email', 'like', "%{$f['buscar']}%")
                                ->orWhere('celular', 'like', "%{$f['buscar']}%");
                        });
                });
            })

            ->when($f['proyecto_id'] ?? null, function ($q) use ($f) {
                $q->where(function ($sub) use ($f) {
                    $sub->where('proyecto_id', $f['proyecto_id'])
                        ->orWhere('reubicado_proyecto_id', $f['proyecto_id']);
                });
            })

            ->when($f['gestor_legal_id'] ?? null, function ($q) use ($f) {
                if ($f['gestor_legal_id'] === 'sin_asignar') {
                    $q->whereNull('gestor_legal_id');
                } else {
                    $q->where('gestor_legal_id', $f['gestor_legal_id']);
                }
            })

            // NUEVO FILTRO GESTOR BACKOFFICE
            ->when($f['filtro_gestor_backoffice'] ?? null, function ($q) use ($f) {
                if ($f['filtro_gestor_backoffice'] === 'sin_asignar') {
                    $q->whereNull('gestor_backoffice_id');
                } else {
                    $q->where('gestor_backoffice_id', $f['filtro_gestor_backoffice']);
                }
            })

            ->when($f['estado_backoffice']                    ?? null, fn($q) => $q->where('estado_backoffice', $f['estado_backoffice']))
            ->when($f['estado_gestor_backoffice']             ?? null, fn($q) => $q->where('estado_gestor_backoffice', $f['estado_gestor_backoffice']))
            ->when($f['estado_contrato_preeliminar_emitido']  ?? null, fn($q) => $q->where('estado_contrato_preeliminar_emitido', $f['estado_contrato_preeliminar_emitido']))
            ->when($f['estado_firma_contrato_firmado']        ?? null, fn($q) => $q->where('estado_firma_contrato_firmado', $f['estado_firma_contrato_firmado']))
            ->when($f['grupo']                                ?? null, fn($q) => $q->where('grupo', $f['grupo']))
            ->when($f['gestor_id']                            ?? null, fn($q) => $q->where('gestor_backoffice_id', $f['gestor_id']))
            ->when($f['estado_cliente_id']                    ?? null, fn($q) => $q->where('estado_cliente_id', $f['estado_cliente_id']))

            ->when(($f['filtro_confirmacion'] ?? '') !== '', function ($q) use ($f) {
                $f['filtro_confirmacion'] === 'pendiente'
                    ? $q->whereNull('preinvitacion_confirmada')
                    : $q->where('preinvitacion_confirmada', $f['filtro_confirmacion']);
            })

            ->when(($f['filtro_invitacion'] ?? '') !== '', function ($q) use ($f) {
                $f['filtro_invitacion'] === 'pendiente'
                    ? $q->whereNull('invitacion_confirmada')
                    : $q->where('invitacion_confirmada', $f['filtro_invitacion']);
            })

            // Rango de fechas (campo: fecha_firma)
            ->when($f['fecha_firma_desde'] ?? null, fn($q) => $q->whereDate('fecha_firma', '>=', $f['fecha_firma_desde']))
            ->when($f['fecha_firma_hasta'] ?? null, fn($q) => $q->whereDate('fecha_firma', '<=', $f['fecha_firma_hasta']))
            // Rango de fechas (campo: fecha_generacion_contrato)
            ->when($f['fecha_generacion_desde'] ?? null, fn($q) => $q->whereDate('fecha_generacion_contrato', '>=', $f['fecha_generacion_desde']))
            ->when($f['fecha_generacion_hasta'] ?? null, fn($q) => $q->whereDate('fecha_generacion_contrato', '<=', $f['fecha_generacion_hasta']));
    }

    // ---------------------------------------------------------------
    // Fillable
    // ---------------------------------------------------------------
    protected $fillable = [
        'entrega_fest_id',
        'proyecto_id',
        'user_id',
        'created_by',
        'updated_by',
        'dni',
        'nombres',
        'email',
        'celular',
        'preinvitacion_confirmada',
        'invitacion_confirmada',
        'lote',
        'manzana',
        'estado_cliente_id',
        'grupo',
        'responsable_llamada_id',
        'responsable_llamada_fecha_asignacion',
        'gestor_backoffice_id',
        'gestor_fecha_asignacion',
        'fecha_culminacion_eecc',
        'link_carpeta_eecc',
        'link_eecc_firmado',
        'estado_gestor_backoffice',
        'observacion_gestor_backoffice',
        'validador_backoffice_id',
        'fecha_validacion_eecc',
        'estado_backoffice',
        'estado_contrato_preeliminar_emitido',
        'estado_firma_contrato_firmado',
        'gestor_legal_id',
        'legal_fecha_asignacion',
        'observacion_gestor_legal',
        'validador_legal_id',
        'fecha_firma_presencial',
        'fecha_validacion_firma',
        'fecha_firma',
        'fecha_generacion_contrato',
        'reubicado_proyecto_id',
        'reubicado_lote',
        'reubicado_manzana',
        'enviado_preinvitacion',
    ];

    protected $casts = [
        'preinvitacion_confirmada' => 'boolean',
        'invitacion_confirmada' => 'boolean',
        'responsable_llamada_fecha_asignacion' => 'datetime',
        'gestor_fecha_asignacion' => 'datetime',
        'fecha_culminacion_eecc' => 'datetime',
        'fecha_validacion_eecc' => 'datetime',
        'fecha_firma' => 'datetime',
        'legal_fecha_asignacion'  => 'datetime',
        'fecha_firma_presencial'  => 'datetime',
        'fecha_validacion_firma'  => 'datetime',
        'fecha_generacion_contrato' => 'datetime',
    ];

    // ---------------------------------------------------------------
    // Relaciones
    // ---------------------------------------------------------------
    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $prospecto): void {
            $userId = Auth::id();

            if ($userId) {
                $prospecto->created_by = $prospecto->created_by ?: $userId;
                $prospecto->updated_by = $userId;
            }
        });

        static::updating(function (self $prospecto): void {
            $userId = Auth::id();

            if ($userId) {
                $prospecto->updated_by = $userId;
            }
        });
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function reubicadoProyecto()
    {
        return $this->belongsTo(Proyecto::class, 'reubicado_proyecto_id');
    }

    public function copropietarios()
    {
        return $this->hasMany(CopropietarioEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function invitado()
    {
        return $this->hasOne(InvitadoEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function acompanantes()
    {
        return $this->hasMany(AcompananteEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function bancarizaciones()
    {
        return $this->hasMany(ProspectoBancarizacionEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_backoffice_id');
    }

    public function gestorLegal()
    {
        return $this->belongsTo(User::class, 'gestor_legal_id');
    }

    public function validadorLegal()
    {
        return $this->belongsTo(User::class, 'validador_legal_id');
    }

    public function responsableLlamada()
    {
        return $this->belongsTo(User::class, 'responsable_llamada_id');
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validador_backoffice_id');
    }

    public function historialComunicaciones()
    {
        return $this->morphMany(\App\Models\Erp\EntregaFest\EntregaFestHistorialComunicacion::class, 'persona');
    }

    public function estadoCliente()
    {
        return $this->belongsTo(EntregaFestEstadoCliente::class, 'estado_cliente_id');
    }

    // ---------------------------------------------------------------
    // Accessors
    // ---------------------------------------------------------------

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres;
    }
}
