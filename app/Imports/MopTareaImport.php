<?php

namespace App\Imports;

use App\Models\EntregaFestMopTarea;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MopTareaImport implements ToCollection, WithHeadingRow
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
                if (empty($row['user_email']) || empty($row['titulo']) || empty($row['instruccion'])) {
                    continue;
                }

                $numFila = $index + 2;

                // Buscar el usuario por email
                $user = User::where('email', trim($row['user_email']))->first();

                if (!$user) {
                    $this->errores[] = "Fila {$numFila}: El usuario con email '{$row['user_email']}' no existe.";
                    continue;
                }

                try {
                    $fase = strtoupper(trim($row['fase'] ?? 'ANTES'));
                    if (!in_array($fase, ['ANTES', 'DURANTE', 'CIERRE'])) {
                        $fase = 'ANTES';
                    }

                    EntregaFestMopTarea::create([
                        'user_id' => $user->id,
                        'entrega_fest_id' => $this->entrega_fest_id,
                        'titulo' => trim($row['titulo']),
                        'fase' => $fase,
                        'instruccion' => trim($row['instruccion']),
                        'esta_completado' => false,
                    ]);

                    $this->importados++;

                } catch (\Exception $e) {
                    $this->errores[] = "Fila {$numFila}: Error al crear la tarea - " . $e->getMessage();
                    Log::error("Error en Importacion de MOP Tarea: " . $e->getMessage());
                }
            }
        });
    }
}
