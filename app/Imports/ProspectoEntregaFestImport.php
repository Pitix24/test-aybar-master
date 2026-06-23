<?php

namespace App\Imports;

use App\Models\ProspectoEntregaFest;
use App\Models\ProspectoHistorico;
use App\Models\CopropietarioEntregaFest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProspectoEntregaFestImport implements ToCollection, WithHeadingRow
{
    protected $entrega_fest_id;
    protected $proyectosValidos;
    public $importados = 0;
    public $nuevos = 0;
    public $actualizados = 0;
    public $filasActualizadas = [];
    public $filasRepetidasExcel = [];
    protected $llavesProcesadas = [];
    public $errores = [];

    public function __construct($entrega_fest_id, $proyectosValidos)
    {
        $this->entrega_fest_id = $entrega_fest_id;
        $this->proyectosValidos = $proyectosValidos;
    }

    public function collection(Collection $rows)
    {
        $clavesProcesadas = []; // Detectar duplicados en el Excel

        DB::transaction(function () use ($rows, &$clavesProcesadas) {
            foreach ($rows as $index => $row) {
                $numFila = $index + 2;

                try {
                    $proyectoId = (int) $row['proyecto_id'];
                    $dniTitular = str_replace("'", "", trim($row['dni'] ?? ''));

                    if (empty($dniTitular)) {
                        $this->errores[] = "Fila {$numFila}: DNI vacío.";
                        continue;
                    }

                    // Detectar columnas
                    $keys = array_map('strtolower', array_keys($row->toArray()));
                    $keyLote = $keys[array_search('lote', $keys)] ?? 'lote';
                    $keyManzana = $keys[array_search('manzana', $keys)] ?? 'manzana';
                    $keyEstado = collect($keys)->first(fn($k) => str_contains($k, 'estado_cliente')) ?? 'estado_cliente';

                    $mza = strtoupper(trim($row[$keyManzana] ?? ''));
                    $lot = strtoupper(trim($row[$keyLote] ?? ''));
                    $estadoExcel = strtoupper(trim($row[$keyEstado] ?? 'ADENDA'));

                    $grupoVal = strtoupper(trim($row['grupo'] ?? 'A'));
                    $grupo = in_array($grupoVal, ['A','B','C','D']) ? $grupoVal : 'A';

                    // ===== DETECTAR DUPLICADOS DENTRO DEL EXCEL =====
                    $claveUnica = "{$proyectoId}-{$dniTitular}-{$lot}-{$mza}";
                    if (isset($clavesProcesadas[$claveUnica])) {
                        $filaOriginal = $clavesProcesadas[$claveUnica];
                        $this->errores[] = "Fila {$numFila}: Duplicado exacto de la fila {$filaOriginal} (mismo DNI {$dniTitular}, Lote {$lot}, Manzana {$mza}).";
                        continue;
                    }
                    $clavesProcesadas[$claveUnica] = $numFila;

                    // ===== Lógica DNI + Lote + Manzana =====
                    $prospecto = ProspectoEntregaFest::where('entrega_fest_id', $this->entrega_fest_id)
                        ->where('proyecto_id', $proyectoId)
                        ->where('dni', $dniTitular)
                        ->where('lote', $lot)
                        ->where('manzana', $mza)
                        ->first();

                    if ($prospecto) {
                        $prospecto->update([
                            'nombres' => $row['nombres'],
                            'email' => $row['email'] ?? '',
                            'celular' => $row['celular'] ?? '',
                            'estado_cliente_id' => \App\Models\EntregaFestEstadoCliente::id($estadoExcel),
                            'grupo' => $grupo,
                            'updated_by' => auth()->id(),
                        ]);
                        $this->actualizados++;
                    } else {
                        ProspectoEntregaFest::create([
                            'entrega_fest_id' => $this->entrega_fest_id,
                            'proyecto_id' => $proyectoId,
                            'dni' => $dniTitular,
                            'user_id' => auth()->id(),
                            'created_by' => auth()->id(),
                            'nombres' => $row['nombres'],
                            'email' => $row['email'] ?? '',
                            'celular' => $row['celular'] ?? '',
                            'lote' => $lot,
                            'manzana' => $mza,
                            'estado_cliente_id' => \App\Models\EntregaFestEstadoCliente::id($estadoExcel),
                            'grupo' => $grupo,
                        ]);
                        $this->nuevos++;
                    }

                    $this->importados++;

                } catch (\Exception $e) {
                    // Capturamos cualquier otro error de base de datos específico para esta fila
                    $this->errores[] = "Fila {$numFila}: Error al guardar (Detalle: " . $e->getMessage() . ")";
                }

                // Detectar columnas
                $keys = array_map('strtolower', array_keys($row->toArray()));
                $keyLote = $keys[array_search('lote', $keys)] ?? 'lote';
                $keyManzana = $keys[array_search('manzana', $keys)] ?? 'manzana';
                $keyEstado = collect($keys)->first(fn($k) => str_contains($k, 'estado_cliente')) ?? 'estado_cliente';

                $mza = strtoupper(trim($row[$keyManzana] ?? ''));
                $lot = strtoupper(trim($row[$keyLote] ?? ''));
                $estadoExcel = strtoupper(trim($row[$keyEstado] ?? 'ADENDA'));

                $grupoVal = strtoupper(trim($row['grupo'] ?? 'A'));
                $grupo = in_array($grupoVal, ['A','B','C','D']) ? $grupoVal : 'A';

                // ===== DETECTAR DUPLICADOS DENTRO DEL EXCEL =====
                $claveUnica = "{$proyectoId}-{$dniTitular}-{$lot}-{$mza}";
                if (isset($clavesProcesadas[$claveUnica])) {
                    $filaOriginal = $clavesProcesadas[$claveUnica];
                    $this->errores[] = "Fila {$numFila}: Duplicado de la fila {$filaOriginal} (mismo DNI {$dniTitular}, Lote {$lot}, Manzana {$mza}).";
                    continue;
                }
                $clavesProcesadas[$claveUnica] = $numFila;

                // =========================================================
                // 1. ALIMENTAR TABLA MAESTRA (Histórico)
                // =========================================================
                $historico = \App\Models\ProspectoHistorico::updateOrCreate(
                    [
                        // Llave maestra
                        'proyecto_id' => $proyectoId,
                        'dni'         => $dniTitular,
                        'lote'        => $lot,
                        'manzana'     => $mza,
                    ],
                    [
                        // Datos vivos
                        'nombres'           => $row['nombres'],
                        'email'             => $row['email'] ?? '',
                        'celular'           => $row['celular'] ?? '',
                        'estado_cliente_id' => \App\Models\EntregaFestEstadoCliente::id($estadoExcel),
                        'grupo'             => $grupo,
                        'updated_by'        => auth()->id(),
                    ]
                );

                if ($historico->wasRecentlyCreated) {
                    $historico->update(['created_by' => auth()->id()]);
                }

                // =========================================================
                // 2. VERIFICAR EN EL EVENTO ACTUAL
                // =========================================================
                $prospecto = ProspectoEntregaFest::where('entrega_fest_id', $this->entrega_fest_id)
                    ->where('prospecto_historico_id', $historico->id) // Búsqueda por la nueva FK
                    ->first();

                if ($prospecto) {
                    // Si ya está en este evento, actualizamos sus datos (comportamiento original)
                    $prospecto->update([
                        'nombres'           => $row['nombres'],
                        'email'             => $row['email'] ?? '',
                        'celular'           => $row['celular'] ?? '',
                        'estado_cliente_id' => \App\Models\EntregaFestEstadoCliente::id($estadoExcel),
                        'grupo'             => $grupo,
                        'updated_by'        => auth()->id(),
                    ]);
                    $this->actualizados++;
                } else {
                    // =========================================================
                    // 3. APAGADO AUTOMÁTICO Y CREACIÓN DEL NUEVO TICKET
                    // =========================================================
                    ProspectoEntregaFest::where('prospecto_historico_id', $historico->id)
                        ->update(['activo' => false]); // Apagar pasados

                    $prospectoNuevo = ProspectoEntregaFest::create([
                        'entrega_fest_id'        => $this->entrega_fest_id,
                        'prospecto_historico_id' => $historico->id, // Conexión
                        'activo'                 => true,           // Nace Activo
                        'proyecto_id'            => $proyectoId,
                        'dni'                    => $dniTitular,
                        'user_id'                => auth()->id(),
                        'created_by'             => auth()->id(),
                        'nombres'                => $row['nombres'],
                        'email'                  => $row['email'] ?? '',
                        'celular'                => $row['celular'] ?? '',
                        'lote'                   => $lot,
                        'manzana'                => $mza,
                        'estado_cliente_id'      => \App\Models\EntregaFestEstadoCliente::id($estadoExcel),
                        'grupo'                  => $grupo,

                        // 🆕 HEREDAR PROGRESO DEL TRÁMITE DESDE EL HISTÓRICO
                        'reubicado_proyecto_id' => $historico->reubicado_proyecto_id,
                        'reubicado_lote' => $historico->reubicado_lote,
                        'reubicado_manzana' => $historico->reubicado_manzana,
                        'estado_backoffice' => $historico->estado_backoffice ?? 'PENDIENTE',
                        'gestor_backoffice_id' => $historico->gestor_backoffice_id,
                        'gestor_fecha_asignacion' => $historico->gestor_fecha_asignacion,
                        'estado_gestor_backoffice' => $historico->estado_gestor_backoffice ?? 'PENDIENTE',
                        'observacion_gestor_backoffice' => $historico->observacion_gestor_backoffice,
                        'fecha_culminacion_eecc' => $historico->fecha_culminacion_eecc,
                        'link_carpeta_eecc' => $historico->link_carpeta_eecc,
                        'link_eecc_firmado' => $historico->link_eecc_firmado,
                        'validador_backoffice_id' => $historico->validador_backoffice_id,
                        'fecha_validacion_eecc' => $historico->fecha_validacion_eecc,
                        'responsable_llamada_id' => $historico->responsable_llamada_id,
                        'responsable_llamada_fecha_asignacion' => $historico->responsable_llamada_fecha_asignacion,
                        'gestor_legal_id' => $historico->gestor_legal_id,
                        'legal_fecha_asignacion' => $historico->legal_fecha_asignacion,
                        'observacion_gestor_legal' => $historico->observacion_gestor_legal,
                        'validador_legal_id' => $historico->validador_legal_id,
                        'fecha_firma_presencial' => $historico->fecha_firma_presencial,
                        'fecha_validacion_firma' => $historico->fecha_validacion_firma,
                        'estado_contrato_preeliminar_emitido' => $historico->estado_contrato_preeliminar_emitido ?? 'PENDIENTE',
                        'estado_firma_contrato_firmado' => $historico->estado_firma_contrato_firmado ?? 'PENDIENTE',
                        'fecha_firma' => $historico->fecha_firma,
                        'fecha_generacion_contrato' => $historico->fecha_generacion_contrato,
                    ]);

                    // Si tienes lógica para procesar copropietarios en el Excel por evento,
                    // la llamas aquí pasándole el ID del nuevo ticket
                    // $this->procesarCopropietario($prospectoNuevo->id, $row, 2);
                    // $this->procesarCopropietario($prospectoNuevo->id, $row, 3);
                    // $this->procesarCopropietario($prospectoNuevo->id, $row, 4);

                    $this->nuevos++;
                }

                $this->importados++;
            }
        });
    }

    private function procesarCopropietario($prospectoId, $row, $suffix)
    {
        $dniKey = "dni_{$suffix}";
        $nombresKey = "nombres_{$suffix}";
        $emailKey = "email_{$suffix}";
        $celularKey = "celular_{$suffix}";

        if (!isset($row[$dniKey]) || empty($row[$dniKey]))
            return;

        $dni = str_replace("'", "", $row[$dniKey]);

        CopropietarioEntregaFest::updateOrCreate(
            [
                'prospecto_entrega_fest_id' => $prospectoId,
                'dni' => $dni,
            ],
            [
                'nombres' => $row[$nombresKey] ?? 'N/A',
                'email' => $row[$emailKey] ?? '',
                'celular' => $row[$celularKey] ?? '',
            ]
        );
    }

    private function limpiarEstado($value)
    {
        if (empty($value))
            return 'PENDIENTE';

        $value = strtoupper(trim($value));

        // Consolidado de todos tus estados de la migración
        $validos = [
            'PENDIENTE',
            'BANCARIZAR',
            'PENALIDAD',
            'OBSERVADO',
            'CONFORME', // BackOffice
            'GENERADO',                                                     // Legal - Preeliminar
            'FIRMADO',                                                       // Legal - Firmado
            'ADENDA',
            'DESISTIMIENTO',
            'DEVOLUCION_DE_APORTES',              // Cliente
            'CARTA_NOTARIAL',
            'PLANTON',
            'RESOLUCION_DE_CONTRATO'
        ];

        return in_array($value, $validos) ? $value : 'PENDIENTE';
    }

    private function transformDate($value)
    {
        if (empty($value))
            return null;

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
            }
            return date('Y-m-d H:i:s', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
    }
}
