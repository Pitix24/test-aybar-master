<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;
use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SedesYAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sedes
        $sedes = [
            ['nombre' => 'Sede Central', 'direccion' => 'Av. Javier Prado 123, San Isidro', 'activo' => true],
            ['nombre' => 'Sede Miraflores', 'direccion' => 'Calle shell 456, Miraflores', 'activo' => true],
            ['nombre' => 'Sede San Isidro', 'direccion' => 'Av. Rivera Navarrete 789, San Isidro', 'activo' => true],
        ];

        foreach ($sedes as $sede) {
            Sede::updateOrCreate(['nombre' => $sede['nombre']], $sede);
        }

        $sedeCentral = Sede::where('nombre', 'Sede Central')->first();
        $sedeMiraflores = Sede::where('nombre', 'Sede Miraflores')->first();
        $sedeSanIsidro = Sede::where('nombre', 'Sede San Isidro')->first();

        // 2. Áreas
        $areas = [
            ['nombre' => 'Soporte Técnico', 'color' => '#3b82f6', 'icono' => 'fa-solid fa-headset', 'activo' => true],
            ['nombre' => 'Atención al Cliente', 'color' => '#10b981', 'icono' => 'fa-solid fa-user-tie', 'activo' => true],
            ['nombre' => 'Ventas', 'color' => '#f59e0b', 'icono' => 'fa-solid fa-cart-shopping', 'activo' => true],
            ['nombre' => 'Logística', 'color' => '#6366f1', 'icono' => 'fa-solid fa-truck', 'activo' => true],
            ['nombre' => 'Recursos Humanos', 'color' => '#ec4899', 'icono' => 'fa-solid fa-users', 'activo' => true],
        ];

        foreach ($areas as $area) {
            $areaModel = Area::updateOrCreate(['nombre' => $area['nombre']], $area);

            // Asignar áreas a sedes (Relación pivot)
            if ($area['nombre'] === 'Soporte Técnico' || $area['nombre'] === 'Atención al Cliente') {
                $areaModel->sedes()->syncWithoutDetaching([$sedeCentral->id, $sedeMiraflores->id, $sedeSanIsidro->id]);
            } else {
                $areaModel->sedes()->syncWithoutDetaching([$sedeCentral->id]);
            }
        }

        // 3. Asignar Usuarios a Áreas
        // Buscamos usuarios con rol 'admin' para asignarles áreas
        $admins = User::where('rol', 'admin')->get();

        if ($admins->isNotEmpty()) {
            $areasList = Area::all();

            foreach ($admins as $admin) {
                // Verificamos que sea una instancia de modelo para evitar errores de stdClass
                if (!$admin instanceof User) {
                    continue;
                }

                // Asignamos 1 o 2 áreas aleatorias a cada admin
                $randomAreas = $areasList->random(min(rand(1, 2), $areasList->count()));

                $isFirst = true;
                foreach ($randomAreas as $area) {
                    $admin->areas()->syncWithoutDetaching([
                        $area->id => [
                            'is_principal' => $isFirst // El primero es el principal
                        ]
                    ]);
                    $isFirst = false;
                }
            }
        }
    }
}
