<?php

namespace Database\Seeders;

use App\Models\UnidadNegocio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadNegocioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnidadNegocio::factory(30)->create();
    }
}
