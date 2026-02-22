<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CitaArchivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cita_id',
        'user_id',
        'nombre_original',
        'path',
        'url',
        'titulo',
        'descripcion',
        'extension',
        'size',
        'mime_type',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
