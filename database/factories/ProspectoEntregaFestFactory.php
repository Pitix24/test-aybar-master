<?php

namespace Database\Factories;

use App\Models\EntregaFest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProspectoEntregaFest>
 */
class ProspectoEntregaFestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $estado = $this->faker->randomElement(['pendiente', 'observado', 'aprobado', 'rechazado']);

        return [
            'entrega_fest_id' => EntregaFest::exists() ? EntregaFest::inRandomOrder()->first()->id : EntregaFest::factory(),
            'proyecto_id' => \App\Models\Proyecto::exists() ? \App\Models\Proyecto::inRandomOrder()->first()->id : \App\Models\Proyecto::factory(),
            'user_id' => User::exists() ? User::inRandomOrder()->first()->id : User::factory(),
            'dni' => $this->faker->unique()->numerify('########'),
            'nombre' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName() . ' ' . $this->faker->lastName(),
            'codigo_cliente' => $this->faker->optional()->numerify('CLI-#####'),
            'codigo_cuota' => $this->faker->optional()->numerify('CUO-#####'),
            'lote' => $this->faker->optional()->numerify('Lote ##'),
            'manzana' => $this->faker->optional()->randomElement(['A', 'B', 'C', 'D', 'E', 'F']),
            'etapa' => $this->faker->optional()->randomElement(['I', 'II', 'III']),
            'estado' => $estado,
            'observacion' => $this->faker->optional(0.3)->sentence(),

            // BackOffice
            'grupo' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'gestor_backoffice_id' => User::exists() ? User::inRandomOrder()->first()->id : null,
            'fecha_culminacion_eecc' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'link_carpeta_eecc' => $this->faker->optional()->url(),
            'link_eecc_firmado' => $this->faker->optional()->url(),
            'validador_backoffice_id' => User::exists() ? User::inRandomOrder()->first()->id : null,
            'fecha_validacion_eecc' => $this->faker->optional(0.5)->dateTimeBetween('-1 month', 'now'),
            'estado_backoffice' => $estado === 'aprobado' ? 'aprobado' : $this->faker->randomElement(['pendiente', 'observado', 'rechazado']),

            // Legal
            'estado_contrato_preeliminar_emitido' => $this->faker->randomElement(['pendiente', 'observado', 'aprobado', 'rechazado']),
            'estado_firma_contrato_firmado' => $this->faker->randomElement(['pendiente', 'observado', 'aprobado', 'rechazado']),
            'fecha_firma' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'fecha_generacion_contrato' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
