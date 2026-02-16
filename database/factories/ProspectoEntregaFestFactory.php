<?php

namespace Database\Factories;

use App\Models\EntregaFest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProspectoEntregaFest>
 */
class ProspectoEntregaFestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entrega_fest_id' => EntregaFest::factory(),
            'user_id' => User::factory(),
            'dni' => $this->faker->unique()->numerify('########'),
            'nombre' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'estado' => $this->faker->randomElement(['pendiente', 'observado', 'aprobado', 'rechazado']),
            'observacion' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
