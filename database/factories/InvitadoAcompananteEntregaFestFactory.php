<?php

namespace Database\Factories;

use App\Models\InvitadoEntregaFest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitadoAcompananteEntregaFest>
 */
class InvitadoAcompananteEntregaFestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invitado_entrega_fest_id' => InvitadoEntregaFest::exists() ? InvitadoEntregaFest::inRandomOrder()->first()->id : InvitadoEntregaFest::factory(),
            'dni' => $this->faker->optional(0.8)->numerify('########'),
            'nombre' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'asistio' => $this->faker->boolean(50),
        ];
    }
}
