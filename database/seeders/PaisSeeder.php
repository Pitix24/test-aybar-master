<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = 'C:\laragon\www\aybar\docs\countries.json';

        if (!File::exists($jsonPath)) {
            $this->command->error("El archivo JSON no existe en la ruta: $jsonPath");
            return;
        }

        $json = File::get($jsonPath);
        $countries = json_decode($json, true);

        if (is_array($countries)) {
            $total = count($countries);
            $this->command->getOutput()->progressStart($total);

            foreach ($countries as $country) {
                $nombreUpper = mb_strtoupper($country['nameES'], 'UTF-8');

                // Evita duplicados: Si el nombre ya existe, no hace nada nuevo.
                DB::table('pais')->updateOrInsert(
                    ['nombre' => $nombreUpper],
                    ['nombre' => $nombreUpper]
                );

                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info('Tabla Pais procesada correctamente sin duplicados.');
        } else {
            $this->command->error('Error al decodificar el archivo JSON.');
        }
    }
}
