<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketEmail extends Model
{
    /** @use HasFactory<\Database\Factories\TicketEmailFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'emisor_id',
        'receptor_id',
        'asunto',
        'mensaje',
        'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}
