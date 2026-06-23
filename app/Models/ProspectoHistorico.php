<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectoHistorico extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyecto_id', 'user_id', 'dni', 'nombres', 'email', 'celular',
        'lote', 'manzana', 'estado_cliente_id', 'grupo', 'gestor_backoffice_id',
        'fecha_culminacion_eecc', 'link_carpeta_eecc', 'link_eecc_firmado',
        'validador_backoffice_id', 'fecha_validacion_eecc', 'estado_backoffice',
        'estado_contrato_preeliminar_emitido', 'estado_firma_contrato_firmado',
        'fecha_firma', 'fecha_generacion_contrato', 'lote_entregado',
        'reubicado_proyecto_id', 'reubicado_lote', 'reubicado_manzana',
        'gestor_fecha_asignacion', 'estado_gestor_backoffice', 'observacion_gestor_backoffice',
        'responsable_llamada_id', 'responsable_llamada_fecha_asignacion',
        'gestor_legal_id', 'legal_fecha_asignacion', 'observacion_gestor_legal',
        'validador_legal_id', 'fecha_firma_presencial', 'fecha_validacion_firma',
        'dni_2', 'nombres_2', 'email_2', 'celular_2',
        'dni_3', 'nombres_3', 'email_3', 'celular_3',
        'dni_4', 'nombres_4', 'email_4', 'celular_4',
        'created_by', 'updated_by'
    ];

    protected $casts = [
        'fecha_culminacion_eecc' => 'datetime',
        'fecha_validacion_eecc' => 'datetime',
        'fecha_firma' => 'datetime',
        'fecha_generacion_contrato' => 'datetime',
        'lote_entregado' => 'boolean',
        'gestor_fecha_asignacion' => 'datetime',
        'responsable_llamada_fecha_asignacion' => 'datetime',
        'legal_fecha_asignacion' => 'datetime',
        'fecha_firma_presencial' => 'datetime',
        'fecha_validacion_firma' => 'datetime',
    ];

    // Relaciones
    public function proyecto() { return $this->belongsTo(Proyecto::class); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function gestorBackoffice() { return $this->belongsTo(User::class, 'gestor_backoffice_id'); }
    public function validadorBackoffice() { return $this->belongsTo(User::class, 'validador_backoffice_id'); }
    public function estadoCliente() { return $this->belongsTo(EntregaFestEstadoCliente::class, 'estado_cliente_id'); }
    public function prospectosEntregaFest() { return $this->hasMany(ProspectoEntregaFest::class, 'prospecto_historico_id'); }

    // Scopes
    public function scopeLoteEntregado($query) {
        return $query->where('lote_entregado', true);
    }

    public function scopeLoteDisponible($query) {
        return $query->where('lote_entregado', false);
    }

    // Helper para extraer copropietarios dinámicamente
    public function coproPietariosExcel(): array
    {
        $copropietarios = [];
        $indices = [2, 3, 4];

        foreach ($indices as $i) {
            if (!empty($this->{"dni_$i"})) {
                $copropietarios[] = [
                    'dni' => $this->{"dni_$i"},
                    'nombres' => $this->{"nombres_$i"},
                    'email' => $this->{"email_$i"},
                    'celular' => $this->{"celular_$i"},
                ];
            }
        }
        return $copropietarios;
    }
}
