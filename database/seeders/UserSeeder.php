<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Direccion;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear un Super Admin específico para pruebas
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@aybar.com',
            'rol' => 'admin',
            'activo' => true,
        ]);
        $superAdmin->assignRole('super-admin');

        $this->createRelatedData($superAdmin);

        // 2. Crear 30 usuarios aleatorios
        $users = User::factory(30)->create();

        foreach ($users as $user) {
            // Si el user tiene rol 'admin' en la tabla, le asignamos un rol de Spatie aleatorio (excepto super-admin)
            if ($user->rol === 'admin') {
                $rolesAdministrativos = [
                    'admin',
                    'supervisor-atc',
                    'asesor-atc',
                    'supervisor-backoffice',
                    'asesor-backoffice',
                    'supervisor-legal',
                    'asesor-legal',
                    'supervisor-archivo',
                    'asesor-archivo',
                ];

                // Aseguramos que sea una instancia de User antes de llamar a assignRole
                if ($user instanceof User) {
                    $user->assignRole(fake()->randomElement($rolesAdministrativos));
                }
            }

            $this->createRelatedData($user);
        }
    }

    /**
     * Crea el cliente y la dirección para un usuario
     */
    private function createRelatedData(User $user)
    {
        Cliente::factory()->create([
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
        ]);

        Direccion::factory()->create([
            'user_id' => $user->id,
        ]);
    }
}
