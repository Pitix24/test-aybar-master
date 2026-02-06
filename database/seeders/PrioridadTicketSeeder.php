<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioridadTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prioridad_tickets')->insert([
            [
                'id' => 1,
                'nombre' => 'Alta',
                'tiempo_permitido' => 24,
                'color' => '#EF4444',
                'icono' => 'fa-solid fa-circle-exclamation',
            ],
            [
                'id' => 2,
                'nombre' => 'Media',
                'tiempo_permitido' => 48,
                'color' => '#F59E0B',
                'icono' => 'fa-solid fa-triangle-exclamation',
            ],
            [
                'id' => 3,
                'nombre' => 'Baja',
                'tiempo_permitido' => 72,
                'color' => '#10B981',
                'icono' => 'fa-solid fa-circle-down',
            ],
        ]);
    }
}
