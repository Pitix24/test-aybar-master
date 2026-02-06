<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrioridadTicket extends Model
{
    /** @use HasFactory<\Database\Factories\PrioridadTicketFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'tiempo_permitido',
        'color',
        'icono',
        'activo',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
