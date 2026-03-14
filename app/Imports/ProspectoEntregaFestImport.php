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

                $prospecto = ProspectoEntregaFest::updateOrCreate(
                    [
                        'entrega_fest_id' => $this->entrega_fest_id,
                        'dni' => $dniTitular,
                        'lote' => $row['lote'], // Añadido para diferenciar si un DNI tiene varios lotes
                    ],
                    [
                        'proyecto_id' => $proyectoId,
                        'user_id' => auth()->id(),
                        'nombres' => $row['nombres'],
                        'email' => $row['email'] ?? '',
                        'celular' => $row['celular'] ?? '',
                        'manzana' => $row['manzana'],
                        'grupo' => in_array($row['grupo'], ['A', 'B', 'C', 'D']) ? $row['grupo'] : 'A',
                        'gestor_backoffice_id' => is_numeric($row['gestor_backoffice_id']) ? $row['gestor_backoffice_id'] : null,
                        'fecha_culminacion_eecc' => $this->transformDate($row['fecha_culminacion_eecc']),
                        'link_carpeta_eecc' => $row['link_carpeta_eecc'],
                        'link_eecc_firmado' => $row['link_eecc_firmado'],
                        'validador_backoffice_id' => is_numeric($row['validador_backoffice_id']) ? $row['validador_backoffice_id'] : null,
                        'fecha_validacion_eecc' => $this->transformDate($row['fecha_validacion_eecc']),
                        'estado_backoffice' => $this->limpiarEstado($row['estado_backoffice']),
                        'estado_contrato_preeliminar_emitido' => $this->limpiarEstado($row['estado_contrato_preeliminar_emitido']),
                        'estado_firma_contrato_firmado' => $this->limpiarEstado($row['estado_firma_contrato_firmado']),
                        'fecha_firma' => $this->transformDate($row['fecha_firma']),
                        'fecha_generacion_contrato' => $this->transformDate($row['fecha_generacion_contrato']),
                    ]
                );

                // Copropietarios
                $this->procesarCopropietario($prospecto->id, $row, '2');
                $this->procesarCopropietario($prospecto->id, $row, '3');
                $this->procesarCopropietario($prospecto->id, $row, '4');

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
        $value = strtolower(trim($value));
        return in_array($value, ['pendiente', 'observado', 'aprobado', 'rechazado']) ? $value : 'pendiente';
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
