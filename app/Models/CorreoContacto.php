<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorreoContacto extends Model
{
    use HasFactory;

    protected $table = 'correo_contactos';

    protected $fillable = [
        'cliente_id',
        'nombres',
        'apellidos',
        'email',
        'activo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function listas()
    {
        return $this->belongsToMany(CorreoLista::class, 'correo_lista_contacto', 'contacto_id', 'lista_id')->withTimestamps();
    }
}
