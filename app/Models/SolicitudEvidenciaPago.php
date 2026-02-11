<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudEvidenciaPago extends Model
{
    /** @use HasFactory<\Database\Factories\SolicitudEvidenciaPagoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',
        'gestor_id',
        'estado_evidencia_pago_id',

        'razon_social',
        'nombre_proyecto',
        'etapa',
        'manzana',
        'lote',

        'codigo_cliente',
        'codigo_cuota',
        'numero_cuota',
        'transaccion_id',
        'fecha_operacion',
        'fecha_vencimiento',
        'monto_operacion',
        'slin_monto',
        'slin_penalidad',
        'slin_numero_operacion',
        'comprobante',
        'ticket',
        'lote_completo',
        'slin_asbanc',
        'slin_evidencia',
        'resuelto_manual',

        'usuario_valida_id',
        'fecha_validacion',
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
        'monto_operacion' => 'decimal:2',
        'slin_monto' => 'decimal:2',
        'slin_penalidad' => 'decimal:2',
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

    public function estado()
    {
        return $this->belongsTo(EstadoSolicitudEvidenciaPago::class, 'estado_evidencia_pago_id');
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function evidencias()
    {
        return $this->hasMany(EvidenciaPago::class);
    }

    public function correos()
    {
        return $this->hasMany(SolicitudEvidenciaPagoEmail::class)
            ->orderByDesc('enviado_at');
    }

    public function getEstaAprobadaAttribute(): bool
    {
        return $this->estado_evidencia_pago_id ===
            EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::APROBADO);
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
