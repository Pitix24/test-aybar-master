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
        // Crear 30 usuarios, cada uno con su perfil de cliente y su dirección
        User::factory(30)->create()->each(function ($user) {
            Cliente::factory()->create([
                'user_id' => $user->id,
                'nombre' => $user->name,
                'email' => $user->email,
            ]);

            Direccion::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
