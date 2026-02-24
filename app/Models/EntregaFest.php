<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\EntregaFestFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gestor_id',
        'nombre',
        'descripcion',
        'codigo',
        'fecha_entrega',
        'activo',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function proyectos()
    {
        return $this->belongsToMany(Proyecto::class, 'entrega_fest_proyecto');
    }

    public function prospectos()
    {
        return $this->hasMany(ProspectoEntregaFest::class);
    }

    public function invitados()
    {
        return $this->hasMany(InvitadoEntregaFest::class);
    }
}
