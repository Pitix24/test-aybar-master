<?php

namespace App\Imports;

use App\Models\EntregaFestItinerarioBloque;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItinerarioImport implements ToCollection, WithHeadingRow
{
    protected $entrega_fest_id;
    public $importados = 0;
    public $errores = [];

    public function __construct($entrega_fest_id)
    {
        $this->entrega_fest_id = $entrega_fest_id;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                if (empty($row['titulo']) || empty($row['hora_inicio'])) {
                    continue;
                }

                $numFila = $index + 2;

                try {
                    // Crear el bloque de itinerario
                    $bloque = EntregaFestItinerarioBloque::create([
                        'entrega_fest_id' => $this->entrega_fest_id,
                        'hora_inicio' => $this->formatTime($row['hora_inicio']),
                        'hora_fin' => !empty($row['hora_fin']) ? $this->formatTime($row['hora_fin']) : null,
                        'titulo' => $row['titulo'],
                        'descripcion' => $row['descripcion'] ?? null,
                        'ubicacion' => $row['ubicacion'] ?? null,
                        'estado' => EntregaFestItinerarioBloque::ESTADO_PENDIENTE,
                        'orden' => $this->importados
                    ]);

                    // Procesar checklist si existe
                    if (!empty($row['checklist'])) {
                        $tareas = explode(';', $row['checklist']);
                        foreach ($tareas as $tarea) {
                            $tarea = trim($tarea);
                            if (!empty($tarea)) {
                                $bloque->checklists()->create([
                                    'tarea' => $tarea,
                                    'esta_listo' => false
                                ]);
                            }
                        }
                    }

                    $this->importados++;

                } catch (\Exception $e) {
                    $this->errores[] = "Fila {$numFila}: Error al crear el bloque - " . $e->getMessage();
                    Log::error("Error en Importacion de Itinerario: " . $e->getMessage());
                }
            }
        });
    }

    private function formatTime($value)
    {
        if (empty($value))
            return null;

        // Si viene como numero de Excel
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('H:i');
        }

        // Si viene como string HH:mm
        return date('H:i', strtotime($value));
    }
}
