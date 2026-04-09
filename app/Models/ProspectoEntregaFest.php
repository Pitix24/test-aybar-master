<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProspectoEntregaFest extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProspectoEntregaFestFactory> */
    use HasFactory, InteractsWithMedia;

    // ---------------------------------------------------------------
    // ENUMS: Grupo
    // ---------------------------------------------------------------
    const GRUPOS = ['A', 'B', 'C', 'D'];

    // ---------------------------------------------------------------
    // ENUMS: Estado BackOffice
    // ---------------------------------------------------------------
    const ESTADO_BACKOFFICE = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'BANCARIZAR' => ['label' => 'Bancarizar', 'color' => '#3B82F6'],
        'PENALIDAD' => ['label' => 'Penalidad', 'color' => '#EF4444'],
        'OBSERVADO' => ['label' => 'Observado', 'color' => '#F59E0B'],
        'CONFORME' => ['label' => 'Conforme', 'color' => '#10B981'],
    ];

    const ESTADO_GESTOR_BACKOFFICE = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'BANCARIZAR' => ['label' => 'Bancarizar', 'color' => '#3B82F6'],
        'PENALIDAD' => ['label' => 'Penalidad', 'color' => '#EF4444'],
        'OBSERVADO' => ['label' => 'Observado', 'color' => '#F59E0B'],
        'CONFORME' => ['label' => 'Conforme', 'color' => '#10B981'],
    ];

    // ---------------------------------------------------------------
    // ENUMS: Estado Contrato Preliminar
    // ---------------------------------------------------------------
    const ESTADO_CONTRATO_PRELIMINAR = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'GENERADO' => ['label' => 'Generado', 'color' => '#3B82F6'],
        'OBSERVADO' => ['label' => 'Observado', 'color' => '#F59E0B'],
        'CONFORME' => ['label' => 'Conforme', 'color' => '#10B981'],
    ];

    // ---------------------------------------------------------------
    // ENUMS: Estado Firma Contrato
    // ---------------------------------------------------------------
    const ESTADO_FIRMA = [
        'PENDIENTE' => ['label' => 'Pendiente', 'color' => '#6B7280'],
        'FIRMADO' => ['label' => 'Firmado', 'color' => '#10B981'],
    ];

    // ---------------------------------------------------------------
    // Helpers: badge color
    // ---------------------------------------------------------------
    public function badgeBackoffice(): string
    {
        return self::ESTADO_BACKOFFICE[$this->estado_backoffice]['color'] ?? '#000000';
    }

    public function badgeGestorBackoffice(): string
    {
        return self::ESTADO_GESTOR_BACKOFFICE[$this->estado_gestor_backoffice]['color'] ?? '#000000';
    }

    public function badgeContratoPreeliminar(): string
    {
        return self::ESTADO_CONTRATO_PRELIMINAR[$this->estado_contrato_preeliminar_emitido]['color'] ?? '#000000';
    }

    public function badgeFirma(): string
    {
        return self::ESTADO_FIRMA[$this->estado_firma_contrato_firmado]['color'] ?? '#000000';
    }

    // ---------------------------------------------------------------
    // Fillable
    // ---------------------------------------------------------------
    protected $fillable = [
        'entrega_fest_id',
        'proyecto_id',
        'user_id',
        'dni',
        'nombres',
        'email',
        'celular',
        'preinvitacion_confirmada',
        'invitacion_confirmada',
        'lote',
        'manzana',
        'estado_cliente_id',
        'grupo',
        'gestor_backoffice_id',
        'gestor_fecha_asignacion',
        'fecha_culminacion_eecc',
        'link_carpeta_eecc',
        'link_eecc_firmado',
        'estado_gestor_backoffice',
        'observacion_gestor_backoffice',
        'validador_backoffice_id',
        'fecha_validacion_eecc',
        'estado_backoffice',
        'estado_contrato_preeliminar_emitido',
        'estado_firma_contrato_firmado',
        'fecha_firma',
        'fecha_generacion_contrato',
        'enviado_preinvitacion',
    ];

    protected $casts = [
        'preinvitacion_confirmada' => 'boolean',
        'invitacion_confirmada' => 'boolean',
    ];

    // ---------------------------------------------------------------
    // Relaciones
    // ---------------------------------------------------------------
    public function entregaFest()
    {
        return $this->belongsTo(EntregaFest::class);
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function copropietarios()
    {
        return $this->hasMany(CopropietarioEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function invitado()
    {
        return $this->hasOne(InvitadoEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function acompanantes()
    {
        return $this->hasMany(AcompananteEntregaFest::class, 'prospecto_entrega_fest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gestor()
    {
        return $this->belongsTo(User::class, 'gestor_backoffice_id');
    }

    public function validador()
    {
        return $this->belongsTo(User::class, 'validador_backoffice_id');
    }

    public function historialComunicaciones()
    {
        return $this->morphMany(\App\Models\Erp\EntregaFest\EntregaFestHistorialComunicacion::class, 'persona');
    }

    public function estadoCliente()
    {
        return $this->belongsTo(EntregaFestEstadoCliente::class, 'estado_cliente_id');
    }

    // ---------------------------------------------------------------
    // Accessors
    // ---------------------------------------------------------------

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres;
    }
}

