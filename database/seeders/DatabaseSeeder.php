<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaisSeeder::class,
            RegionSeeder::class,
            ProvinciaSeeder::class,
            DistritoSeeder::class,
            RolesYPermisosSeeder::class,

            UnidadNegocioSeeder::class,
            GrupoProyectoSeeder::class,
            ProyectoSeeder::class,
            UserSeeder::class,
        ]);
    }
}
