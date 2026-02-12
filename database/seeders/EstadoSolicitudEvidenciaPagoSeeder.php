<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoSolicitudEvidenciaPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['id' => 1, 'nombre' => 'PENDIENTE', 'color' => '#6c757d', 'icono' => 'fa-clock'],
            ['id' => 2, 'nombre' => 'RECHAZADO', 'color' => '#dc3545', 'icono' => 'fa-circle-xmark'],
            ['id' => 3, 'nombre' => 'APROBADO', 'color' => '#198754', 'icono' => 'fa-circle-check'],
        ];

        foreach ($estados as $estado) {
            \App\Models\EstadoSolicitudEvidenciaPago::updateOrCreate(['id' => $estado['id']], $estado);
        }
    }
}
