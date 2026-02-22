<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappPlantilla extends Model
{
    protected $fillable = ['nombre', 'contenido', 'categoria'];

    public function campanas()
    {
        return $this->hasMany(WhatsappCampana::class, 'plantilla_id');
    }
}
