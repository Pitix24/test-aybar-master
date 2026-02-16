<?php

namespace Database\Factories;

use App\Models\InvitadoEntregaFest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AsistenciaEntregaFest>
 */
class AsistenciaEntregaFestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invitado_entrega_fest_id' => InvitadoEntregaFest::factory(['confirmado' => true]),
            'user_id' => User::factory(),
            'fecha_checkin' => $this->faker->dateTimeThisMonth(),
            'metodo' => $this->faker->randomElement(['qr', 'manual', 'dni']),
        ];
    }
}
