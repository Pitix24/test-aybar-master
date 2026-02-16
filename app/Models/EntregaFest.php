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
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',
        'user_id',
        'nombre',
        'descripcion',
        'codigo',
        'fecha_entrega',
        'activo',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
