<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CitaEmail extends Model
{
    /** @use HasFactory<\Database\Factories\CitaEmailFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cita_id',
        'emisor_id',
        'receptor_id',
        'asunto',
        'mensaje',
        'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
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
