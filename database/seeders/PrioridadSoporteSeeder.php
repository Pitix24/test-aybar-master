<?php

namespace Database\Seeders;

use App\Models\Erp\Soporte\PrioridadSoporte;
use Illuminate\Database\Seeder;

class PrioridadSoporteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['nombre' => 'BAJA', 'color' => '#10B981', 'icono' => 'fa-solid fa-circle-down', 'activo' => true],
            ['nombre' => 'MEDIA', 'color' => '#F59E0B', 'icono' => 'fa-solid fa-triangle-exclamation', 'activo' => true],
            ['nombre' => 'ALTA', 'color' => '#F97316', 'icono' => 'fa-solid fa-circle-exclamation', 'activo' => true],
            ['nombre' => 'CRITICA', 'color' => '#EF4444', 'icono' => 'fa-solid fa-radiation', 'activo' => true],
        ];

        foreach ($items as $item) {
            PrioridadSoporte::updateOrCreate(
                ['nombre' => $item['nombre']],
                $item
            );
        }
    }
}
