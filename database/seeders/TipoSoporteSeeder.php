<?php

namespace Database\Seeders;

use App\Models\Erp\Soporte\TipoSoporte;
use Illuminate\Database\Seeder;

class TipoSoporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['nombre' => 'BUG', 'color' => '#DC2626', 'icono' => 'fa-solid fa-bug', 'activo' => true],
            ['nombre' => 'MEJORA', 'color' => '#2563EB', 'icono' => 'fa-solid fa-wand-magic-sparkles', 'activo' => true],
            ['nombre' => 'IMPLEMENTACION', 'color' => '#7C3AED', 'icono' => 'fa-solid fa-gears', 'activo' => true],
            ['nombre' => 'CONSULTA', 'color' => '#0EA5E9', 'icono' => 'fa-solid fa-circle-question', 'activo' => true],
        ];

        foreach ($items as $item) {
            TipoSoporte::updateOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
