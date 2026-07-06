<?php

namespace App\Imports;

use App\Models\ProspectoHistorico;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class ProspectoHistoricoImport implements ToCollection, WithHeadingRow
{
    public $nuevos = 0;
    public $actualizados = 0;
    public $errores = 0;
    public $detalleErrores = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                // Validación básica de campos obligatorios
                if (empty($row['proyecto_id']) || empty($row['dni'])) {
                    continue; // Saltamos filas vacías
                }

                // Buscamos si ya existe en el histórico
                $historico = ProspectoHistorico::where('proyecto_id', $row['proyecto_id'])
                    ->where('dni', $row['dni'])
                    ->where('lote', $row['lote'])
                    ->where('manzana', $row['manzana'])
                    ->first();

                // Procesamos el flag clave: lote_entregado (Soporta 'SI', '1', true, etc.)
                $loteEntregado = in_array(strtoupper(trim($row['lote_entregado'] ?? '')), ['SI', '1', 'TRUE', 'VERDADERO', 'YES']);

                $datosFormateados = [
                    'user_id' => $row['user_id'] ?? null,
                    'nombres' => $row['nombres'],
                    'email' => $row['email'] ?? null,
                    'celular' => $row['celular'] ?? null,

                    'estado_cliente_id' => $row['estado_cliente_id'] ?? null,
                    'grupo' => $row['grupo'] ?? 'A',
                    'gestor_backoffice_id' => $row['gestor_backoffice_id'] ?? null,

                    // Aseguramos que las fechas se guarden nulas si el excel viene vacío
                    'fecha_culminacion_eecc' => !empty($row['fecha_culminacion_eecc']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_culminacion_eecc']) : null,
                    'link_carpeta_eecc' => $row['link_carpeta_eecc'] ?? null,
                    'link_eecc_firmado' => $row['link_eecc_firmado'] ?? null,

                    'validador_backoffice_id' => $row['validador_backoffice_id'] ?? null,
                    'fecha_validacion_eecc' => !empty($row['fecha_validacion_eecc']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_validacion_eecc']) : null,

                    'estado_backoffice' => $row['estado_backoffice'] ?? 'PENDIENTE',
                    'estado_contrato_preeliminar_emitido' => $row['estado_contrato_preeliminar_emitido'] ?? 'PENDIENTE',
                    'estado_firma_contrato_firmado' => $row['estado_firma_contrato_firmado'] ?? 'PENDIENTE',

                    'fecha_firma' => !empty($row['fecha_firma']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_firma']) : null,
                    'fecha_generacion_contrato' => !empty($row['fecha_generacion_contrato']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_generacion_contrato']) : null,

                    'lote_entregado' => $loteEntregado,

                    // Copropietarios (Datos Planos)
                    'dni_2' => $row['dni_2'] ?? null,
                    'nombres_2' => $row['nombres_2'] ?? null,
                    'email_2' => $row['email_2'] ?? null,
                    'celular_2' => $row['celular_2'] ?? null,

                    'dni_3' => $row['dni_3'] ?? null,
                    'nombres_3' => $row['nombres_3'] ?? null,
                    'email_3' => $row['email_3'] ?? null,
                    'celular_3' => $row['celular_3'] ?? null,

                    'dni_4' => $row['dni_4'] ?? null,
                    'nombres_4' => $row['nombres_4'] ?? null,
                    'email_4' => $row['email_4'] ?? null,
                    'celular_4' => $row['celular_4'] ?? null,

                    'updated_by' => auth()->id(),
                ];

                if ($historico) {
                    $historico->update($datosFormateados);
                    $this->actualizados++;
                } else {
                    $datosFormateados['proyecto_id'] = $row['proyecto_id'];
                    $datosFormateados['dni'] = $row['dni'];
                    $datosFormateados['lote'] = $row['lote'];
                    $datosFormateados['manzana'] = $row['manzana'];
                    $datosFormateados['created_by'] = auth()->id();

                    ProspectoHistorico::create($datosFormateados);
                    $this->nuevos++;
                }

            } catch (\Exception $e) {
                $this->errores++;
                $this->detalleErrores[] = "Fila " . ($index + 2) . " (DNI: " . ($row['dni'] ?? 'N/A') . "): " . $e->getMessage();
                Log::error("Error importando histórico fila " . ($index + 2) . ": " . $e->getMessage());
            }
        }
    }
}
