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
        $clavesProcesadas = []; // Detectar duplicados en el Excel

        DB::transaction(function () use ($rows, &$clavesProcesadas) {
            foreach ($rows as $index => $row) {

                $numFila = $index + 2;
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
                    $this->errores[] = "Fila {$numFila}: Duplicado de la fila {$filaOriginal} (mismo DNI {$dniTitular}, Lote {$lot}, Manzana {$mza}).";
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
