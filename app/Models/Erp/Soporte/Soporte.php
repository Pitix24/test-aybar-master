<?php

namespace App\Models\Erp\Soporte;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Soporte extends Model
{
    use SoftDeletes;

    protected $table = 'soportes';

    protected $fillable = [
        'codigo',
        'tipo_soporte_id',
        'prioridad_soporte_id',
        'area_id',
        'estado_soporte_id',
        'cierre_soporte_id',
        'titulo',
        'descripcion',
        'observaciones',
        'solicitante_id',
        'gestor_id',
        'assigned_at',
        'resuelto_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resuelto_at' => 'datetime',
    ];

    // ─── Relaciones con Catálogos ───────────────────────────────────────────────

    public function tipoSoporte()
    {
        return $this->belongsTo(TipoSoporte::class, 'tipo_soporte_id');
    }

    public function prioridadSoporte()
    {
        return $this->belongsTo(PrioridadSoporte::class, 'prioridad_soporte_id');
    }

    public function estadoSoporte()
    {
        return $this->belongsTo(EstadoSoporte::class, 'estado_soporte_id');
    }

    public function cierreSoporte()
    {
        return $this->belongsTo(CierreSoporte::class, 'cierre_soporte_id');
    }

    // ─── Auto-generación de código SP-XXXX ───────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Soporte $soporte): void {
            if (empty($soporte->codigo)) {
                $ultimo = self::withTrashed()->max('id') ?? 0;
                $soporte->codigo = 'SP-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            }

            if (empty($soporte->solicitante_id)) {
                $soporte->solicitante_id = Auth::id();
            }

            $soporte->created_by = Auth::id();
        });

        static::updating(function (Soporte $soporte): void {
            $soporte->updated_by = Auth::id();

            // Note: If you want to auto-set resuelto_at based on a specific estado name:
            // This is harder when it's dynamic. But let's check if the related estado is "RESUELTO" or "CERRADO"
            if ($soporte->isDirty('estado_soporte_id') && empty($soporte->resuelto_at)) {
                $estado = EstadoSoporte::find($soporte->estado_soporte_id);
                if ($estado && in_array($estado->nombre, ['RESUELTO', 'CERRADO'])) {
                    $soporte->resuelto_at = now();
                }
            }
        });
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // Relación con el usuario solicitante
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    // Relación con el usuario gestor
    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    // Relación con el usuario que creó
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con el usuario que actualizó
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relación con el usuario que eliminó
    public function eliminador()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // ─── Relación Polimórfica con Archivos ─────────────────────────────────────

    public function archivos()
    {
        return $this->morphMany(SoporteArchivo::class, 'archivable');
    }
}
