<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CopropietarioEntregaFest extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospecto_entrega_fest_id',
        'dni',
        'nombres',
        'email',
        'celular',
    ];

    /**
     * El prospecto titular del lote al que pertenece este copropietario.
     * Desde aquí se obtiene lote, manzana, entrega_fest_id, proyecto_id.
     */
    public function prospecto()
    {
        return $this->belongsTo(ProspectoEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    /**
     * El invitado generado para este copropietario (si se le generó una invitación).
     */
    public function invitado()
    {
        return $this->hasOne(InvitadoEntregaFest::class, 'copropietario_entrega_fest_id');
    }
}
