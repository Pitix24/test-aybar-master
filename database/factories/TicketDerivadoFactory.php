<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketDerivado>
 */
class TicketDerivadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => \App\Models\Ticket::factory(),
            'de_area_id' => \App\Models\Area::factory(),
            'a_area_id' => \App\Models\Area::factory(),
            'usuario_deriva_id' => \App\Models\User::factory(),
            'usuario_recibe_id' => \App\Models\User::factory(),
            'motivo' => $this->faker->paragraph(),
        ];
    }
}
