<?php

namespace Database\Seeders;

use App\Models\EntregaFestEstadoCliente;
use Illuminate\Database\Seeder;

class EntregaFestEstadoClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EntregaFestEstadoCliente::insert([
            ['nombre' => 'ADENDA', 'color' => '#6B7280', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'DESISTIMIENTO', 'color' => '#EF4444', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'DEVOLUCION_DE_APORTES', 'color' => '#EF4444', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'CARTA_NOTARIAL', 'color' => '#F59E0B', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'PLANTON', 'color' => '#EF4444', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'RESOLUCION_DE_CONTRATO', 'color' => '#EF4444', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'VENDIDO', 'color' => '#10B981', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
