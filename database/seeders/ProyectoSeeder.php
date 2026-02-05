<?php

namespace Database\Seeders;

use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = UnidadNegocio::all();
        $grupos = GrupoProyecto::all();

        if ($unidades->isEmpty() || $grupos->isEmpty()) {
            return;
        }

        Proyecto::factory(30)
            ->recycle($unidades)
            ->recycle($grupos)
            ->create();
    }
}
