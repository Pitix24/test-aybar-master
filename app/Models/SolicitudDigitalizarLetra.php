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
        'gestor_id',
        'estado_solicitud_digitalizar_letra_id',

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

        'observacion',

        //DB ANTIGUO
        'dni',
        'nombres',
        'email',
        'celular',
        'direccion',
        'region',
        'provincia',
        'distrito',
        'origen',

        //SUPERVISOR
        'usuario_valida_id',
        'fecha_validacion',

        //AUDITORIA
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function estado()
    {
        return $this->belongsTo(EstadoSolicitudDigitalizarLetra::class, 'estado_solicitud_digitalizar_letra_id');
    }

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
            'envio_cavali_solicituds',
            'solicitud_digitalizar_letras_id',
            'envios_cavali_id'
        );
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function validadoPor()
    {
        return $this->belongsTo(User::class, 'usuario_valida_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function eliminadoPor()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function booted()
    {
        static::creating(function ($ticket) {
            if (auth()->check()) {
                $ticket->created_by = auth()->id();
            }
        });

        static::updating(function ($ticket) {
            if (auth()->check()) {
                $ticket->updated_by = auth()->id();
            }
        });

        static::deleting(function ($ticket) {
            if (auth()->check()) {
                $ticket->deleted_by = auth()->id();
                $ticket->saveQuietly();
            }
        });
    }
}
