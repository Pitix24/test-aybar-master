<?php

namespace Database\Seeders;

use App\Models\Erp\Soporte\CierreSoporte;
use Illuminate\Database\Seeder;

class CierreSoporteSeeder extends Seeder
{
    public function run(): void
    {
        CierreSoporte::updateOrCreate(
            ['nombre' => 'RESUELTO_CLIENTE'],
            [
                'color' => '#10B981',
                'icono' => 'fa-solid fa-user-check',
                'activo' => true,
            ]
        );

        CierreSoporte::updateOrCreate(
            ['nombre' => 'RESUELTO_EQUIPO'],
            [
                'color' => '#3B82F6',
                'icono' => 'fa-solid fa-handshake',
                'activo' => true,
            ]
        );

        CierreSoporte::updateOrCreate(
            ['nombre' => 'DUPLICADO'],
            [
                'color' => '#F59E0B',
                'icono' => 'fa-solid fa-copy',
                'activo' => true,
            ]
        );

        CierreSoporte::updateOrCreate(
            ['nombre' => 'NO_ES_PROBLEMA'],
            [
                'color' => '#8B5CF6',
                'icono' => 'fa-solid fa-circle-xmark',
                'activo' => true,
            ]
        );

        CierreSoporte::updateOrCreate(
            ['nombre' => 'FALTA_INFORMACION'],
            [
                'color' => '#EF4444',
                'icono' => 'fa-solid fa-exclamation-triangle',
                'activo' => true,
            ]
        );
    }
}
