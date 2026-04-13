<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoLibroReclamacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            [
                'nombre' => 'NUEVO',
                'descripcion' => 'Reclamación ingresada y pendiente de revisión',
                'color' => 'blue',
                'es_final' => false,
                'orden' => 1,
            ],
            [
                'nombre' => 'EN_GESTION',
                'descripcion' => 'En revisión por equipo legal',
                'color' => 'yellow',
                'es_final' => false,
                'orden' => 2,
            ],
            [
                'nombre' => 'OBSERVADO',
                'descripcion' => 'Requiere observaciones del cliente',
                'color' => 'orange',
                'es_final' => false,
                'orden' => 3,
            ],
            [
                'nombre' => 'RESUELTO',
                'descripcion' => 'Resolución favorable al cliente',
                'color' => 'green',
                'es_final' => true,
                'orden' => 4,
            ],
            [
                'nombre' => 'NO_PROCEDE',
                'descripcion' => 'La reclamación no procede',
                'color' => 'red',
                'es_final' => true,
                'orden' => 5,
            ],
            [
                'nombre' => 'CERRADO',
                'descripcion' => 'Caso cerrado',
                'color' => 'gray',
                'es_final' => true,
                'orden' => 6,
            ],
        ];

        foreach ($estados as $estado) {
            DB::table('estado_libro_reclamaciones')->insert([
                'nombre' => $estado['nombre'],
                'descripcion' => $estado['descripcion'],
                'color' => $estado['color'],
                'es_final' => $estado['es_final'],
                'orden' => $estado['orden'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
