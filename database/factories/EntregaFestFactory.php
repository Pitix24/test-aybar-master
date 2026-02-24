<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntregaFest>
 */
class EntregaFestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gestor_id' => User::exists() ? User::inRandomOrder()->first()->id : User::factory(),
            'nombre' => $this->faker->sentence(3) . ' Fest',
            'descripcion' => $this->faker->paragraph(),
            'codigo' => 'EF-' . $this->faker->unique()->numberBetween(1000, 9999),
            'fecha_entrega' => $this->faker->dateTimeBetween('now', '+6 months'),
            'activo' => true,
        ];
    }
}
