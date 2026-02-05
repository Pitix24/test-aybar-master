<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'dni' => fake()->unique()->numerify('########'),
            'telefono_principal' => fake()->phoneNumber(),
            'telefono_alternativo' => fake()->phoneNumber(),
        ];
    }
}
