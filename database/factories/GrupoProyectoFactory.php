<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GrupoProyecto>
 */
class GrupoProyectoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = $this->faker->unique()->sentence(3);
        return [
            'nombre' => $nombre,
            'slug' => \Illuminate\Support\Str::slug($nombre),
            'activo' => $this->faker->boolean(),
        ];
    }
}
