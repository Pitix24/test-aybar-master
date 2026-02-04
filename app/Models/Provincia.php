<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    /** @use HasFactory<\Database\Factories\ProvinciaFactory> */
    use HasFactory;

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function distritos()
    {
        return $this->hasMany(Distrito::class, 'provincia_id');
    }
}
