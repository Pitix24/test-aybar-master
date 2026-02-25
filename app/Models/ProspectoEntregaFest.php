<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectoEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\ProspectoEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'entrega_fest_id',
        'proyecto_id',
        'user_id',
        'dni',
        'nombre',
        'apellidos',
        'email',
        'celular',
        'codigo_cliente',
        'codigo_cuota',
        'lote',
        'manzana',
        'etapa',
        'estado',
        'observacion',
        'grupo',
        'gestor_backoffice_id',
        'fecha_culminacion_eecc',
        'link_carpeta_eecc',
        'link_eecc_firmado',
        'validador_backoffice_id',
        'fecha_validacion_eecc',
        'estado_backoffice',
        'estado_contrato_preeliminar_emitido',
        'estado_firma_contrato_firmado',
        'fecha_firma',
        'fecha_generacion_contrato',
    ];

    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function invitado()
    {
        return $this->hasOne(InvitadoEntregaFest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}
