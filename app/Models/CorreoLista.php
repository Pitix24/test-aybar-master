<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorreoLista extends Model
{
    use HasFactory;

    protected $table = 'correo_listas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function contactos()
    {
        return $this->belongsToMany(CorreoContacto::class, 'correo_lista_contacto', 'lista_id', 'contacto_id')->withTimestamps();
    }

    public function campanas()
    {
        return $this->hasMany(CorreoCampana::class, 'lista_id');
    }
}
