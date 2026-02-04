<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    /** @use HasFactory<\Database\Factories\DistritoFactory> */
    use HasFactory;

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
}
