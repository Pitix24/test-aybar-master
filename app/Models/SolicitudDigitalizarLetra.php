<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudDigitalizarLetra extends Model
{
    /** @use HasFactory<\Database\Factories\SolicitudDigitalizarLetraFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unidad_negocio_id',
        'proyecto_id',
        'cliente_id',

        'lote_completo',
        'codigo_cuota',

        'razon_social',
        'nombre_proyecto',
        'etapa',
        'manzana',
        'lote',
        'codigo_cliente',
        'numero_cuota',
        'codigo_venta',
        'fecha_vencimiento',
        'importe_cuota',
        'estado_cavali',
    ];

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function userCliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function enviosCavali()
    {
        return $this->belongsToMany(
            EnvioCavali::class,
            'envio_cavali_solicitud',
            'solicitud_digitalizar_letras_id',
            'envios_cavali_id'
        );
    }
}
