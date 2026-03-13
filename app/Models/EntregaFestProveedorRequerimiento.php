<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class EntregaFestProveedorRequerimiento extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'entrega_fest_proveedor_requerimientos';
    protected $fillable = [
        "proveedor_id", 
        "requerimiento", 
        "esta_cubierto",
        "user_id",
        "completado_at"
    ];

    protected $casts = [
        'esta_cubierto' => 'boolean',
        'completado_at' => 'datetime',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('evidencias')
            ->singleFile();
    }

    public function proveedor()
    {
        return $this->belongsTo(EntregaFestProveedor::class, "proveedor_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
