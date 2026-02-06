<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Canal;

class CanalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Canal::insert([
            ['nombre' => 'Call Center', 'activo' => true],
            ['nombre' => 'Whatsapp', 'activo' => true],
            ['nombre' => 'Presencial', 'activo' => true],
            ['nombre' => 'Libro Reclamación', 'activo' => true],
            ['nombre' => 'Redes Sociales', 'activo' => true],
        ]);
    }
}
