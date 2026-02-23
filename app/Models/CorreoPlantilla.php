<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorreoPlantilla extends Model
{
    use HasFactory;

    protected $table = 'correo_plantillas';

    protected $fillable = [
        'nombre',
        'asunto',
        'cuerpo',
        'variables',
    ];

    protected $casts = [
        'variables' => 'json',
    ];

    public function campanas()
    {
        return $this->hasMany(CorreoCampana::class, 'plantilla_id');
    }
}
