<?php

namespace App\Models\Erp\Soporte;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoporteArchivo extends Model
{
    /** @use HasFactory<\Database\Factories\SoporteArchivoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'archivable_id',
        'archivable_type',
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

    /**
     * Obtener el modelo al que pertenece el archivo (Soporte o SoporteMensaje).
     */
    public function archivable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
