<?php

namespace Database\Seeders;

use App\Models\AsistenciaEntregaFest;
use App\Models\Cliente;
use App\Models\EntregaFest;
use App\Models\InvitadoAcompananteEntregaFest;
use App\Models\InvitadoEntregaFest;
use App\Models\InvitadoEnvioEntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntregaFestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener o crear usuarios para auditoría
        $admin = User::where('email', 'admin@example.com')->first() ?? User::factory()->create(['email' => 'admin@example.com', 'name' => 'Admin']);
        $gestores = User::count() > 3 ? User::inRandomOrder()->take(3)->get() : User::factory()->count(3)->create();

        // Limpiar datos previos del seeder para evitar duplicados
        EntregaFest::whereIn('codigo', ['EF-2026-001', 'EF-2026-002'])->each(function (EntregaFest $ef) {
            $ef->forceDelete(); // Usar forceDelete si tiene SoftDeletes
        });

        // 1. Crear Eventos Principales
        $festivales = [
            [
                'nombre' => 'Gran Entrega Fest Verano 2026',
                'codigo' => 'EF-2026-001',
                'descripcion' => 'Evento masivo de entrega de lotes y festival para propietarios.',
            ],
            [
                'nombre' => 'Invierno Fest Aybar 2026',
                'codigo' => 'EF-2026-002',
                'descripcion' => 'Segunda edición anual de entrega de proyectos.',
            ]
        ];

        foreach ($festivales as $data) {
            $unidad = UnidadNegocio::inRandomOrder()->first() ?? UnidadNegocio::factory()->create();

            $evento = EntregaFest::create([
                'unidad_negocio_id' => $unidad->id,
                'cliente_id' => Cliente::inRandomOrder()->first()?->id ?? Cliente::factory()->create()->id,
                'user_id' => $admin->id,
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'codigo' => $data['codigo'],
                'fecha_entrega' => now()->addMonths(rand(1, 6)),
                'activo' => true,
            ]);

            // Asociar proyectos al festival (Relación Many-to-Many - Pivot)
            $proyectos = Proyecto::count() > 5 ? Proyecto::inRandomOrder()->take(rand(1, 3))->get() : Proyecto::factory()->count(2)->create();
            $evento->proyectos()->attach($proyectos->pluck('id'));

            // 2. Crear Prospectos para este festival
            foreach ($proyectos as $proyecto) {
                $prospectos = ProspectoEntregaFest::factory()
                    ->count(rand(10, 20))
                    ->create([
                        'entrega_fest_id' => $evento->id,
                        'proyecto_id' => $proyecto->id,
                        'user_id' => $gestores->random()->id,
                        'gestor_backoffice_id' => $gestores->random()->id,
                    ]);

                // 3. Convertir a Invitados (Solo los aprobados o una parte)
                $prospectosAprobados = $prospectos->where('estado', 'aprobado');

                foreach ($prospectosAprobados as $prospecto) {
                    $invitado = InvitadoEntregaFest::create([
                        'entrega_fest_id' => $evento->id,
                        'prospecto_entrega_fest_id' => $prospecto->id,
                        'codigo_invitado' => 'QR-' . strtoupper(bin2hex(random_bytes(4))),
                        'cantidad_acompanantes_permitidos' => rand(1, 4),
                        'confirmado' => $this->shouldConfirm(),
                    ]);

                    // 4. Agregar Acompañantes para algunos invitados
                    if (rand(0, 1)) {
                        InvitadoAcompananteEntregaFest::factory()
                            ->count(rand(1, $invitado->cantidad_acompanantes_permitidos))
                            ->create(['invitado_entrega_fest_id' => $invitado->id]);
                    }

                    // 5. Simular Envíos/Contactos
                    InvitadoEnvioEntregaFest::create([
                        'invitado_entrega_fest_id' => $invitado->id,
                        'canal' => $this->randomCanal(),
                        'estado' => $this->randomEstadoEnvio(),
                        'detalle' => 'Gestión de invitación realizada.',
                        'user_id' => $gestores->random()->id,
                        'fecha_envio' => now()->subDays(rand(1, 10)),
                    ]);

                    // 6. Simular Asistencia (si está confirmado y por azar)
                    if ($invitado->confirmado && rand(0, 1)) {
                        AsistenciaEntregaFest::create([
                            'invitado_entrega_fest_id' => $invitado->id,
                            'user_id' => $gestores->random()->id,
                            'fecha_checkin' => now()->subHours(rand(1, 24)),
                            'metodo' => rand(0, 1) ? 'qr' : 'manual',
                        ]);
                    }
                }
            }
        }
    }

    private function shouldConfirm(): bool
    {
        return rand(0, 10) > 3;
    }

    private function randomCanal(): string
    {
        return ['correo', 'whatsapp', 'llamada'][rand(0, 2)];
    }

    private function randomEstadoEnvio(): string
    {
        return ['pendiente', 'enviado', 'fallido', 'confirmado'][rand(0, 3)];
    }
}
