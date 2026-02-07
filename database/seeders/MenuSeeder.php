<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = public_path('erp-menu-principal.json');

        if (!file_exists($jsonPath)) {
            return;
        }

        $menuData = json_decode(file_get_contents($jsonPath), true);

        $this->importMenu($menuData);
    }

    private function importMenu(array $items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            $menu = \App\Models\Menu::create([
                'parent_id' => $parentId,
                'nombre' => $item['nombre'],
                'ruta' => $item['ruta'] ?? '#',
                'url' => $item['url'] ?? '#',
                'icono' => $item['icono'] ?? null,
                'nivel' => $item['nivel'] ?? 1,
                'orden' => $index,
                'roles' => $item['roles'] ?? [],
                'permisos' => $item['permisos'] ?? [],
                'activo' => true,
            ]);

            if (!empty($item['submenus'])) {
                $this->importMenu($item['submenus'], $menu->id);
            }
        }
    }
}
