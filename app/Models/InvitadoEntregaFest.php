<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitadoEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\InvitadoEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'entrega_fest_id',
        'prospecto_entrega_fest_id',
        'copropietario_entrega_fest_id',
        'codigo_invitado',
        'cantidad_acompanantes_permitidos',
        'confirmado',
        'estado_confirmacion',
        'transporte',
        'observaciones_asistencia',
    ];

    protected $casts = [
        'confirmado' => 'boolean',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    /**
     * Si el invitado es un copropietario.
     */
    public function copropietario()
    {
        return $this->belongsTo(CopropietarioEntregaFest::class, 'copropietario_entrega_fest_id');
    }

    public function asistencia()
    {
        return $this->hasOne(AsistenciaEntregaFest::class, 'invitado_entrega_fest_id');
    }

    /**
     * Devuelve el nombre del invitado (titular o copropietario).
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->prospecto?->nombres
            ?? $this->copropietario?->nombres
            ?? 'Sin nombre';
    }

    /**
     * Devuelve el lote del invitado (siempre en el prospecto titular).
     * Si es copropietario, lo busca a través de su prospecto.
     */
    public function getLoteAttribute(): ?string
    {
        return $this->prospecto?->lote
            ?? $this->copropietario?->prospecto?->lote;
    }

    /**
     * Devuelve la manzana del invitado.
     */
    public function getManzanaAttribute(): ?string
    {
        return $this->prospecto?->manzana
            ?? $this->copropietario?->prospecto?->manzana;
    }

    /**
     * Indica si el invitado es el titular o un copropietario.
     */
    public function getTipoAttribute(): string
    {
        return $this->prospecto_entrega_fest_id ? 'Titular' : 'Copropietario';
    }
}
