<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingArchivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'marketing_archivos';

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

    public function archivable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
