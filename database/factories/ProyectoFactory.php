<?php

namespace Database\Factories;

use App\Models\GrupoProyecto;
use App\Models\UnidadNegocio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proyecto>
 */
class ProyectoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unidad_negocio_id' => UnidadNegocio::factory(),
            'grupo_proyecto_id' => GrupoProyecto::factory(),
            'nombre' => $this->faker->unique()->sentence(4),
            'slin_id' => $this->faker->bothify('PRO-####'),
            'activo' => $this->faker->boolean(),
        ];
    }
}
