<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectoBancarizacionEntregaFest extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrega_fest_id',
        'prospecto_entrega_fest_id',
        'cuota',
        'importe',
        'fecha_deposito_real',
        'estado',
    ];

    protected $casts = [
        'fecha_deposito_real' => 'date',
        'importe' => 'decimal:2',
    ];

    const ESTADO = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'BANCARIZADO' => ['label' => 'Bancarizado', 'color' => '#3B82F6'],
    ];

    public function badgeEstado(): string
    {
        return self::ESTADO[$this->estado]['color'] ?? '#000000';
    }

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }
}
