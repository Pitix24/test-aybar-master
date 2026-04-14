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
    ];

    protected $casts = [
        'fecha_deposito_real' => 'date',
        'importe' => 'decimal:2',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }
}
