<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketMensaje extends Model
{
    /** @use HasFactory<\Database\Factories\TicketMensajeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'mensaje',
        'es_interno',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener los archivos adjuntos al mensaje.
     */
    public function archivos()
    {
        return $this->morphMany(TicketArchivo::class, 'archivable');
    }
}
