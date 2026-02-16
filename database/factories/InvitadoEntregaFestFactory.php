<?php

namespace Database\Factories;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitadoEntregaFest>
 */
class InvitadoEntregaFestFactory extends Factory
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
            'prospecto_entrega_fest_id' => ProspectoEntregaFest::factory(['estado' => 'aprobado']),
            'codigo_invitado' => 'QR-' . $this->faker->unique()->numberBetween(100000, 999999),
            'cantidad_acompanantes_permitidos' => $this->faker->numberBetween(1, 4),
            'confirmado' => $this->faker->boolean(70),
        ];
    }
}
