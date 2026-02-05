<?php

namespace Database\Seeders;

use App\Models\GrupoProyecto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrupoProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GrupoProyecto::factory(30)->create();
    }
}
