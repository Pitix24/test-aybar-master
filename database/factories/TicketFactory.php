<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use App\Models\Proyecto;
use App\Models\SubTipoSolicitud;
use App\Models\TipoSolicitud;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unidad_negocio_id' => UnidadNegocio::inRandomOrder()->first()?->id ?? UnidadNegocio::factory(),
            'proyecto_id' => Proyecto::inRandomOrder()->first()?->id ?? Proyecto::factory(),
            'cliente_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'gestor_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'area_id' => Area::inRandomOrder()->first()?->id ?? Area::factory(),
            'tipo_solicitud_id' => TipoSolicitud::inRandomOrder()->first()?->id ?? TipoSolicitud::factory(),
            'sub_tipo_solicitud_id' => SubTipoSolicitud::inRandomOrder()->first()?->id ?? SubTipoSolicitud::factory(),
            'canal_id' => Canal::inRandomOrder()->first()?->id ?? Canal::factory(),
            'estado_ticket_id' => EstadoTicket::inRandomOrder()->first()?->id ?? EstadoTicket::factory(),
            'prioridad_ticket_id' => PrioridadTicket::inRandomOrder()->first()?->id ?? PrioridadTicket::factory(),
            'asunto_inicial' => $this->faker->sentence(),
            'descripcion_inicial' => $this->faker->paragraph(),
            'lotes' => null,
            'asunto_respuesta' => $this->faker->optional()->sentence(),
            'descripcion_respuesta' => $this->faker->optional()->paragraph(),
            'dni' => $this->faker->numerify('########'),
            'nombres' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'direccion' => $this->faker->address(),
            'origen' => $this->faker->randomElement(['slin', 'clientes_2']),
            'usuario_valida_id' => User::inRandomOrder()->first()?->id,
            'fecha_validacion' => $this->faker->optional()->dateTime(),
            'created_by' => User::inRandomOrder()->first()?->id,
            'updated_by' => User::inRandomOrder()->first()?->id,
        ];
    }
}
