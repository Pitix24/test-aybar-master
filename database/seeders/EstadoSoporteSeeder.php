<?php

namespace Database\Seeders;

use App\Models\Erp\Soporte\EstadoSoporte;
use Illuminate\Database\Seeder;

class EstadoSoporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['nombre' => 'ABIERTO', 'color' => '#2563EB', 'icono' => 'fa-solid fa-folder-open', 'activo' => true],
            ['nombre' => 'EN_PROGRESO', 'color' => '#F59E0B', 'icono' => 'fa-solid fa-spinner', 'activo' => true],
            ['nombre' => 'EN_REVISION', 'color' => '#0EA5E9', 'icono' => 'fa-solid fa-magnifying-glass', 'activo' => true],
            ['nombre' => 'RESUELTO', 'color' => '#10B981', 'icono' => 'fa-solid fa-circle-check', 'activo' => true],
            ['nombre' => 'CERRADO', 'color' => '#6B7280', 'icono' => 'fa-solid fa-lock', 'activo' => true],
        ];

        foreach ($items as $item) {
            EstadoSoporte::updateOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
