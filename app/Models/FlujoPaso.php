<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlujoPaso extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_solicitud_id',
        'nombre_paso',
        'orden',
        'descripcion'
    ];

    public function tipoSolicitud()
    {
        return $this->belongsTo(TipoSolicitud::class);
    }

    public function ticketPasos()
    {
        return $this->hasMany(TicketPaso::class);
    }
}
