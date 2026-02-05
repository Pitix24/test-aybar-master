<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    /** @use HasFactory<\Database\Factories\DireccionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'region_id',
        'provincia_id',
        'distrito_id',
        'direccion',
        'direccion_numero',
        'opcional',
        'codigo_postal',
        'referencia',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the region associated with the address.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the provincia associated with the address.
     */
    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    /**
     * Get the distrito associated with the address.
     */
    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }
}
