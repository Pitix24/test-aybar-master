<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketArchivo>
 */
class TicketArchivoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = $this->faker->fileExtension();
        return [
            'archivable_id' => 1,
            'archivable_type' => 'App\Models\Ticket',
            'user_id' => User::factory(),
            'nombre_original' => $this->faker->word() . '.' . $extension,
            'path' => 'tickets/' . $this->faker->uuid() . '.' . $extension,
            'extension' => $extension,
            'size' => $this->faker->numberBetween(1000, 5000000),
            'mime_type' => $this->faker->mimeType(),
        ];
    }
}
