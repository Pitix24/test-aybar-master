<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tutorial;
use App\Models\MarketingArchivo;

class TutorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tutoriales = [
            [
                'titulo' => 'Cómo descargar Boletas',
                'video_id' => 'dumEsf9xjLI',
                'thumb' => 'assets/youtube/Descargar-Boletas-Miniatura-1280x720.png',
                'orden' => 1
            ],
            [
                'titulo' => 'Cómo subir Voucher de Pago',
                'video_id' => 'i3iTZZt84Zk',
                'thumb' => 'assets/youtube/Voucher-de-Pago-Miniatura-1280x720.png',
                'orden' => 2
            ],
            [
                'titulo' => 'Cómo descargar Estado de Cuenta',
                'video_id' => 'Tcq8L3J8h7s',
                'thumb' => 'assets/youtube/Estado-de-Cuenta-Miniatura-1280x720.png',
                'orden' => 3
            ],
            [
                'titulo' => 'Cómo descargar Cronograma de Pago',
                'video_id' => 'I76FbY5L8PM',
                'thumb' => 'assets/youtube/Cronograma-de-Pagos-Miniatura-1280x720.png',
                'orden' => 4
            ],
        ];

        foreach ($tutoriales as $item) {
            $tutorial = Tutorial::updateOrCreate(
                ['video_id' => $item['video_id']],
                [
                    'titulo' => $item['titulo'],
                    'orden' => $item['orden'],
                    'activo' => true
                ]
            );

            // Crear el registro de la miniatura si no existe
            MarketingArchivo::updateOrCreate(
                [
                    'archivable_id' => $tutorial->id,
                    'archivable_type' => Tutorial::class
                ],
                [
                    'nombre_original' => basename($item['thumb']),
                    'path' => $item['thumb'],
                    'url' => asset($item['thumb']),
                    'extension' => 'png',
                    'size' => 0,
                    'mime_type' => 'image/png'
                ]
            );
        }
    }
}
