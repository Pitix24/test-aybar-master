<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoClienteDocumento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipo_cliente_documentos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'orden',
        'color',
        'icono',
        'icono_documentos',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function clienteDocumentos()
    {
        return $this->hasMany(ClienteDocumento::class, 'tipo_cliente_documentos_id');
    }
}
