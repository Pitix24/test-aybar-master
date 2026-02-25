<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectoEntregaFest extends Model
{
    /** @use HasFactory<\Database\Factories\ProspectoEntregaFestFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'entrega_fest_id',
        'proyecto_id',
        'user_id',
        'dni',
        'nombres',
        'email',
        'celular',
        'lote',
        'manzana',
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

    protected static function booted()
    {
        static::creating(function ($prospecto) {
            $prospecto->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }

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
        return $this->nombres;
    }
}
