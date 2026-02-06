<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketEmail>
 */
class TicketEmailFactory extends Factory
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
            'emisor_id' => \App\Models\User::factory(),
            'receptor_id' => \App\Models\User::factory(),
            'asunto' => $this->faker->sentence(),
            'mensaje' => $this->faker->paragraphs(3, true),
        ];
    }
}
