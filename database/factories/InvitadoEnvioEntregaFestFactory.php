<?php

namespace Database\Factories;

use App\Models\InvitadoEntregaFest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitadoEnvioEntregaFest>
 */
class InvitadoEnvioEntregaFestFactory extends Factory
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
            'canal' => $this->faker->randomElement(['correo', 'whatsapp', 'llamada']),
            'estado' => $this->faker->randomElement(['pendiente', 'enviado', 'fallido', 'confirmado']),
            'detalle' => $this->faker->optional(0.5)->sentence(),
            'user_id' => User::exists() ? User::inRandomOrder()->first()->id : User::factory(),
            'fecha_envio' => $this->faker->dateTimeThisMonth(),
        ];
    }
}
