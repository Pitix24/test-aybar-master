<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioCavali extends Model
{
    /** @use HasFactory<\Database\Factories\EnvioCavaliFactory> */
    protected $table = 'envios_cavali';

    protected $fillable = [
        'fecha_corte',
        'unidad_negocio_id',
        'estado_solicitud_digitalizar_letra_id',
        'enviado_at',
        'archivo_zip',
    ];

    protected $casts = [
        'fecha_corte' => 'date',
        'enviado_at' => 'datetime',
    ];

    public function estado()
    {
        return $this->belongsTo(EstadoSolicitudDigitalizarLetra::class, 'estado_solicitud_digitalizar_letra_id');
    }

    public function unidadNegocio()
    {
        return $this->belongsTo(UnidadNegocio::class);
    }

    public function solicitudes()
    {
        return $this->belongsToMany(
            SolicitudDigitalizarLetra::class,
            'envio_cavali_solicitud',
            'envios_cavali_id',
            'solicitud_digitalizar_letras_id'
        );
    }

    public function getArchivoNombreAttribute()
    {
        return $this->archivo_zip ? basename($this->archivo_zip) : null;
    }
}
