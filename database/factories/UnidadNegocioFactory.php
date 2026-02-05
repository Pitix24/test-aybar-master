<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnidadNegocio>
 */
class UnidadNegocioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->company(),
            'razon_social' => $this->faker->company(),
            'ruc' => $this->faker->unique()->numerify('###########'),
            'slin_id' => $this->faker->unique()->numerify('#####'),
            // CAVALI fields
            'cavali_girador_tipo_documento' => $this->faker->randomElement(['DNI', 'RUC']),
            'cavali_girador_documento' => $this->faker->numerify('########'),
            'cavali_girador_nombre' => $this->faker->firstName(),
            'cavali_girador_apellido' => $this->faker->lastName(),
            'cavali_girador_email' => $this->faker->email(),
            'cavali_girador_telefono' => $this->faker->phoneNumber(),
            'activo' => $this->faker->boolean(),
        ];
    }
}
