<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPaso extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'flujo_paso_id',
        'completado',
        'fecha_completado',
        'user_id'
    ];

    protected $casts = [
        'completado' => 'boolean',
        'fecha_completado' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function flujoPaso()
    {
        return $this->belongsTo(FlujoPaso::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
