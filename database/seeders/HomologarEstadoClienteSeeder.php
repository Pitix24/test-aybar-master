<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomologarEstadoClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('relacionar.csv');

        if (!file_exists($path)) {
            $this->command->error("El archivo CSV no existe en: $path");
            return;
        }

        $file = fopen($path, 'r');
        
        // Saltar cabecera
        fgetcsv($file, 0, ';');

        $contador = 0;

        $this->command->info('Iniciando homologación de estados de clientes...');

        DB::transaction(function () use ($file, &$contador) {
            while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
                if (isset($data[0]) && isset($data[1])) {
                    $prospectoId = $data[0];
                    $estadoId = $data[1];

                    DB::table('prospecto_entrega_fests')
                        ->where('id', $prospectoId)
                        ->update(['estado_cliente_id' => $estadoId]);
                    
                    $contador++;
                }
            }
        });

        fclose($file);

        $this->command->info("¡Homologación completada! Se actualizaron $contador registros.");
    }
}
