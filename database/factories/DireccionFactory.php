<?php

namespace Database\Factories;

use App\Models\Distrito;
use App\Models\Provincia;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Direccion>
 */
class DireccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $distrito = Distrito::inRandomOrder()->first() ?? Distrito::factory()->create();
        $provincia = $distrito->provincia;
        $region = $provincia->region;

        return [
            'user_id' => User::factory(),
            'region_id' => $region->id,
            'provincia_id' => $provincia->id,
            'distrito_id' => $distrito->id,
            'direccion' => fake()->streetAddress(),
            'direccion_numero' => fake()->buildingNumber(),
            'opcional' => fake()->secondaryAddress(),
            'codigo_postal' => fake()->postcode(),
            'referencia' => fake()->sentence(),
        ];
    }
}
