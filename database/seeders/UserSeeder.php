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
        $this->command->info('Creando usuarios administrativos...');

        // 1. Super Admin
        $super_admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super_admin@aybar.com',
            'rol' => 'admin',
            'activo' => true,
        ]);
        $super_admin->assignRole('super-admin');
        $this->createRelatedData($super_admin);
        $this->command->info('✓ Super Admin creado: admin@aybar.com');

        // 1. Admin
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@aybar.com',
            'rol' => 'admin',
            'activo' => true,
        ]);
        $admin->assignRole('admin');
        $this->createRelatedData($admin);
        $this->command->info('✓ Admin creado: admin@aybar.com');

        // 2. Supervisor ATC
        $supervisor_atc = User::factory()->create([
            'name' => 'Supervisor ATC',
            'email' => 'supervisor_atc@aybar.com',
            'rol' => 'admin',
            'activo' => true,
        ]);
        $supervisor_atc->assignRole('supervisor-atc');
        $this->createRelatedData($supervisor_atc);
        $this->command->info('✓ Supervisor ATC creado: supervisor@aybar.com');

        // 3. Asesor ATC
        $asesor_atc = User::factory()->create([
            'name' => 'Asesor ATC',
            'email' => 'asesor_atc@aybar.com',
            'rol' => 'admin',
            'activo' => true,
        ]);
        $asesor_atc->assignRole('asesor-atc');
        $this->createRelatedData($asesor_atc);
        $this->command->info('✓ Asesor ATC creado: asesor@aybar.com');

        $this->command->newLine();
        $this->command->info('Creando clientes específicos...');

        // 4. Clientes
        /*$clientes = User::factory(50)->cliente()->create();

        foreach ($clientes as $cliente) {
            $this->createRelatedData($cliente);
        }*/

        // 5. Admins
        $admins = User::factory(20)->admin()->create();

        foreach ($admins as $admin) {
            // Si el user tiene rol 'admin' en la tabla, le asignamos un rol de Spatie aleatorio (excepto super-admin)
            $rolesAdministrativos = [
                'admin',
                'supervisor-atc',
                'asesor-atc',
            ];

            // Aseguramos que sea una instancia de User antes de llamar a assignRole
            if ($admin instanceof User) {
                $admin->assignRole(fake()->randomElement($rolesAdministrativos));
                $this->createRelatedData($admin);
            }
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
