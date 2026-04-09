<?php

namespace App\Imports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketImport implements ToCollection, WithHeadingRow
{
    public $importados = 0;
    public $errores = [];
    public $filasImportadasData = [];
    public $columnasExcel = [];

    public function collection(Collection $rows)
    {
        // Capturar columnas reales del Excel para debug en el log
        if ($rows->isNotEmpty()) {
            $this->columnasExcel = array_keys($rows->first()->toArray());
            Log::info('[TICKET-IMPORT] Columnas detectadas en Excel: ', $this->columnasExcel);
        }

        foreach ($rows as $index => $row) {
            $numFila = $index + 2;
            $rowArray = $row->toArray();

            // Helper insensible a mayúsculas/espacios
            $get = function (string $key) use ($rowArray) {
                if (array_key_exists($key, $rowArray)) {
                    $val = $rowArray[$key];
                    return ($val === '' || $val === null) ? null : $val;
                }
                $keyNorm = strtolower(trim($key));
                foreach ($rowArray as $k => $v) {
                    if (strtolower(trim($k)) === $keyNorm) {
                        return ($v === '' || $v === null) ? null : $v;
                    }
                }
                return null;
            };

            $asunto      = $get('asunto_inicial');
            $descripcion = $get('descripcion_inicial');

            // Saltar filas completamente vacías
            if (empty($asunto) && empty($descripcion)) {
                continue;
            }

            // Si falta alguno de los dos, usar el otro como fallback
            $asunto      = $asunto ?? $descripcion;
            $descripcion = $descripcion ?? $asunto;

            try {
                DB::beginTransaction();

                $ticketData = [
                    'unidad_negocio_id'     => $this->toInt($get('unidad_negocio_id')),
                    'proyecto_id'           => $this->toInt($get('proyecto_id')),
                    'cliente_id'            => $this->toInt($get('cliente_id')),
                    'gestor_id'             => $this->toInt($get('gestor_id')),
                    'area_id'               => $this->toInt($get('area_id')),
                    'ticket_padre_id'       => $this->toInt($get('ticket_padre_id')),
                    'tipo_solicitud_id'     => $this->toInt($get('tipo_solicitud_id')),
                    'sub_tipo_solicitud_id' => $this->toInt($get('sub_tipo_solicitud_id')),
                    'canal_id'              => $this->toInt($get('canal_id')),
                    'estado_ticket_id'      => $this->toInt($get('estado_ticket_id')) ?? 1,
                    'prioridad_ticket_id'   => $this->toInt($get('prioridad_ticket_id')) ?? 3,

                    'asunto_inicial'        => $asunto,
                    'descripcion_inicial'   => $descripcion,
                    'lotes'                 => $this->parseLotes($get('lotes')),

                    'asunto_respuesta'      => $get('asunto_respuesta'),
                    'descripcion_respuesta' => $get('descripcion_respuesta'),

                    'dni'                   => $this->limpiarTexto($get('dni')),
                    'nombres'               => $get('nombres'),
                    'email'                 => $get('email'),
                    'celular'               => $this->limpiarTexto($get('celular')),
                    'direccion'             => $get('direccion'),
                    'origen'                => $get('origen') ?? 'antiguo',

                    'usuario_valida_id'     => $this->toInt($get('usuario_valida_id')),
                    'fecha_validacion'      => $this->transformDate($get('fecha_validacion')),

                    'created_by'            => $this->toInt($get('created_by')),
                    'updated_by'            => $this->toInt($get('updated_by')),
                    'deleted_by'            => $this->toInt($get('deleted_by')),
                ];

                $ticket = new Ticket();
                $ticket->fill($ticketData);

                // Forzar timestamps históricos si vienen en el Excel
                if ($get('created_at')) $ticket->created_at = $this->transformDate($get('created_at'));
                if ($get('updated_at')) $ticket->updated_at = $this->transformDate($get('updated_at'));
                if ($get('deleted_at')) $ticket->deleted_at = $this->transformDate($get('deleted_at'));

                $ticket->save();

                $this->filasImportadasData[] = [
                    'id'      => $ticket->id,
                    'asunto'  => $ticket->asunto_inicial,
                    'nombres' => $ticket->nombres,
                    'dni'     => $ticket->dni,
                    'fecha'   => $ticket->created_at->format('d/m/Y H:i'),
                ];

                $this->importados++;
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $msg = "Fila {$numFila}: " . $e->getMessage();
                $this->errores[] = $msg;
                Log::error("[TICKET-IMPORT] {$msg}");
            }
        }
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') return null;
        return is_numeric($value) ? (int) $value : null;
    }

    private function limpiarTexto($value): ?string
    {
        if ($value === null) return null;
        return (string) str_replace("'", "", $value);
    }

    private function parseLotes($value): ?array
    {
        if (empty($value)) return null;
        if (is_array($value)) return $value;
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        return [$value];
    }

    private function transformDate($value): ?string
    {
        if ($value === null || $value === '') return null;
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d H:i:s');
            }
            $ts = strtotime((string) $value);
            return $ts ? date('Y-m-d H:i:s', $ts) : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
