<?php

namespace Database\Seeders;

use App\Models\EstadoTicket;
use Illuminate\Database\Seeder;

class EstadoTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstadoTicket::insert([
            [
                'nombre' => 'Nuevo',
                'color' => '#3498db', // Azul
                'icono' => 'fa-solid fa-envelope-open-text',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'En Gestión',
                'color' => '#f1c40f', // Amarillo
                'icono' => 'fa-solid fa-spinner',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Derivado',
                'color' => '#9b59b6', // Morado
                'icono' => 'fa-solid fa-share-nodes',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'En Espera Cliente',
                'color' => '#e67e22', // Naranja
                'icono' => 'fa-solid fa-user-clock',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Atendido',
                'color' => '#95a5a6', // Gris
                'icono' => 'fa-solid fa-building-circle-exclamation',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Cerrado',
                'color' => '#2ecc71', // Verde
                'icono' => 'fa-solid fa-circle-check',
                'activo' => true,
                'created_at' => now(),
            ],
        ]);
    }
}
