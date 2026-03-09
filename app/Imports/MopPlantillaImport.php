<?php

namespace App\Imports;

use App\Models\EntregaFestMopPlantilla;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MopPlantillaImport implements ToCollection, WithHeadingRow
{
    public $importados = 0;
    public $errores = [];

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                if (empty($row['rol_nombre']) || empty($row['instruccion'])) {
                    continue;
                }

                $numFila = $index + 2;

                try {
                    $fase = strtoupper(trim($row['fase'] ?? 'ANTES'));
                    if (!in_array($fase, ['ANTES', 'DURANTE', 'CIERRE'])) {
                        $fase = 'ANTES';
                    }

                    EntregaFestMopPlantilla::create([
                        'rol_nombre' => trim($row['rol_nombre']),
                        'fase' => $fase,
                        'instruccion' => trim($row['instruccion']),
                        'prioridad' => is_numeric($row['prioridad']) ? (int) $row['prioridad'] : 1,
                    ]);

                    $this->importados++;

                } catch (\Exception $e) {
                    $this->errores[] = "Fila {$numFila}: Error - " . $e->getMessage();
                    Log::error("Error en Importacion de MOP Plantilla: " . $e->getMessage());
                }
            }
        });
    }
}
