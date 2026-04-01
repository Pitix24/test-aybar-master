<?php

namespace App\Imports;

use App\Models\ProspectoEntregaFest;
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
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                // El WithHeadingRow usa los nombres de las columnas del Excel como keys del array
                // Según lo que vimos en el Excel anterior:
                // proyecto_id, user_id, dni, nombres, email, celular, lote, manzana, grupo ...

                /*if (empty($row['dni']))
                    continue;*/

                $numFila = $index + 2;
                $proyectoId = (int) $row['proyecto_id'];

                Log::info("Fila {$numFila}: Validando Proyecto ID", [
                    'proyectoId_excel' => $proyectoId,
                    'proyectosValidos' => $this->proyectosValidos,
                    'is_valid' => in_array($proyectoId, $this->proyectosValidos)
                ]);

                if (!in_array($proyectoId, array_map('intval', $this->proyectosValidos))) {
                    $this->errores[] = "Fila {$numFila}: El proyecto ID {$proyectoId} no está asignado a este evento.";
                    continue;
                }

                $dniTitular = str_replace("'", "", $row['dni']);

                // Detectar las llaves correctas por si tienen espacios o mayúsculas en el Excel
                $keyLote = collect(array_keys($row->toArray()))->filter(fn($k) => trim(strtolower($k)) == 'lote')->first() ?? 'lote';
                $keyManzana = collect(array_keys($row->toArray()))->filter(fn($k) => trim(strtolower($k)) == 'manzana')->first() ?? 'manzana';
                $keyEstado = collect(array_keys($row->toArray()))->filter(fn($k) => str_contains(trim(strtolower($k)), 'estado_cliente'))->first() ?? 'estado_cliente';

                $mza = (string) strtoupper(trim($row[$keyManzana] ?? ''));
                $lot = (string) strtoupper(trim($row[$keyLote] ?? ''));
                $estadoExcel = (string) strtoupper(trim($row[$keyEstado] ?? 'ADENDA'));

                $proy = (string) $proyectoId;
                $llaveFila = $proy . '-' . $mza . '-' . $lot;

                if (isset($this->llavesProcesadas[$llaveFila])) {
                    $this->filasRepetidasExcel[] = $index + 2;
                }
                $this->llavesProcesadas[$llaveFila] = true;

                $grupoVal = strtoupper(trim($row['grupo'] ?? 'A'));
                $grupo = in_array($grupoVal, ['A', 'B', 'C', 'D']) ? $grupoVal : 'A';

                $prospecto = ProspectoEntregaFest::updateOrCreate(
                    [
                        'entrega_fest_id' => (int) $this->entrega_fest_id,
                        'proyecto_id' => (int) $proyectoId,
                        'lote' => $lot,
                        'manzana' => $mza,
                    ],
                    [
                        'dni' => $dniTitular,
                        'user_id' => auth()->id(),
                        'nombres' => $row['nombres'],
                        'email' => $row['email'] ?? '',
                        'celular' => $row['celular'] ?? '',
                        'estado_cliente' => $estadoExcel,
                        'grupo' => $grupo,
                        'gestor_backoffice_id' => is_numeric($row['gestor_backoffice_id'] ?? null) ? $row['gestor_backoffice_id'] : null,
                        'fecha_culminacion_eecc' => $this->transformDate($row['fecha_culminacion_eecc'] ?? null),
                        'link_carpeta_eecc' => $row['link_carpeta_eecc'] ?? null,
                        'link_eecc_firmado' => $row['link_eecc_firmado'] ?? null,
                        'validador_backoffice_id' => is_numeric($row['validador_backoffice_id'] ?? null) ? $row['validador_backoffice_id'] : null,
                        'fecha_validacion_eecc' => $this->transformDate($row['fecha_validacion_eecc'] ?? null),
                        'estado_backoffice' => $this->limpiarEstado($row['estado_backoffice'] ?? 'PENDIENTE'),
                        'estado_contrato_preeliminar_emitido' => $this->limpiarEstado($row['estado_contrato_preeliminar_emitido'] ?? 'PENDIENTE'),
                        'estado_firma_contrato_firmado' => $this->limpiarEstado($row['estado_firma_contrato_firmado'] ?? 'PENDIENTE'),
                        'fecha_firma' => $this->transformDate($row['fecha_firma'] ?? null),
                        'fecha_generacion_contrato' => $this->transformDate($row['fecha_generacion_contrato'] ?? null),
                    ]
                );

                // Copropietarios
                $this->procesarCopropietario($prospecto->id, $row, '2');
                $this->procesarCopropietario($prospecto->id, $row, '3');
                $this->procesarCopropietario($prospecto->id, $row, '4');

                if ($prospecto->wasRecentlyCreated) {
                    $this->nuevos++;
                } else {
                    $this->actualizados++;
                    $this->filasActualizadas[] = $index + 2;
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
