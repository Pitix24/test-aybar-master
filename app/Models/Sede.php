<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sede extends Model
{
    /** @use HasFactory<\Database\Factories\SedeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'direccion',
        'activo',
    ];
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function areas()
    {
        return $this->belongsToMany(Area::class)->withTimestamps();
    }
}
