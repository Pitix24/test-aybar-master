<?php

namespace Database\Seeders;

use App\Models\AsistenciaEntregaFest;
use App\Models\CopropietarioEntregaFest;
use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\Proyecto;
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
        $admin = User::where('email', 'admin@aybar.com')->first()
            ?? User::factory()->create(['email' => 'admin@aybar.com', 'name' => 'Admin']);
        $gestores = User::count() > 3
            ? User::inRandomOrder()->take(3)->get()
            : User::factory()->count(3)->create();

        // Limpiar datos previos para evitar duplicados
        EntregaFest::whereIn('codigo', ['EF-2026-001', 'EF-2026-002'])->each(function (EntregaFest $ef) {
            $ef->forceDelete();
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
            ],
        ];

        foreach ($festivales as $data) {
            $evento = EntregaFest::create([
                'gestor_id' => $gestores->random()->id,
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'],
                'codigo' => $data['codigo'],
                'fecha_entrega' => now()->addMonths(rand(1, 6)),
                'activo' => true,
            ]);

            // Asociar proyectos al festival (Pivot)
            $proyectos = Proyecto::count() > 5
                ? Proyecto::inRandomOrder()->take(rand(1, 3))->get()
                : Proyecto::factory()->count(2)->create();
            $evento->proyectos()->attach($proyectos->pluck('id'));

            // 2. Crear Prospectos (titulares de lote) para este festival
            foreach ($proyectos as $proyecto) {
                $prospectos = ProspectoEntregaFest::factory()
                    ->count(rand(2, 4))
                    ->create([
                        'entrega_fest_id' => $evento->id,
                        'proyecto_id' => $proyecto->id,
                        'user_id' => $gestores->random()->id,
                        'gestor_backoffice_id' => $gestores->random()->id,
                    ]);

                foreach ($prospectos as $prospecto) {

                    // 3. Crear Copropietarios (0 a 2 por lote)
                    $cantidadCopropietarios = rand(0, 2);
                    $copropietarios = collect();

                    for ($i = 0; $i < $cantidadCopropietarios; $i++) {
                        $copropietarios->push(CopropietarioEntregaFest::create([
                            'prospecto_entrega_fest_id' => $prospecto->id,
                            'dni' => $this->randomDni(),
                            'nombres' => fake()->name(),
                            'email' => fake()->safeEmail(),
                            'celular' => '9' . rand(10000000, 99999999),
                        ]));
                    }

                    // 4. Crear Invitado para el TITULAR (si está aprobado)
                    if ($prospecto->estado === 'aprobado') {
                        $invitadoTitular = InvitadoEntregaFest::create([
                            'entrega_fest_id' => $evento->id,
                            'prospecto_entrega_fest_id' => $prospecto->id,
                            'copropietario_entrega_fest_id' => null,
                            'codigo_invitado' => 'QR-' . strtoupper(bin2hex(random_bytes(4))),
                            'cantidad_acompanantes_permitidos' => rand(1, 4),
                            'confirmado' => $this->shouldConfirm(),
                            'estado_confirmacion' => 'pendiente',
                        ]);

                        // 5. Simular Asistencia del titular
                        if ($invitadoTitular->confirmado && rand(0, 1)) {
                            AsistenciaEntregaFest::create([
                                'invitado_entrega_fest_id' => $invitadoTitular->id,
                                'user_id' => $gestores->random()->id,
                                'fecha_checkin' => now()->subHours(rand(1, 24)),
                                'metodo' => rand(0, 1) ? 'qr' : 'manual',
                            ]);
                        }
                    }

                    // 6. Crear Invitado para cada COPROPIETARIO (independientemente del titular)
                    foreach ($copropietarios as $copropietario) {
                        $invitadoCoprop = InvitadoEntregaFest::create([
                            'entrega_fest_id' => $evento->id,
                            'prospecto_entrega_fest_id' => null,
                            'copropietario_entrega_fest_id' => $copropietario->id,
                            'codigo_invitado' => 'QR-' . strtoupper(bin2hex(random_bytes(4))),
                            'cantidad_acompanantes_permitidos' => rand(0, 2),
                            'confirmado' => $this->shouldConfirm(),
                            'estado_confirmacion' => 'pendiente',
                        ]);

                        // 7. Simular Asistencia del copropietario
                        if ($invitadoCoprop->confirmado && rand(0, 1)) {
                            AsistenciaEntregaFest::create([
                                'invitado_entrega_fest_id' => $invitadoCoprop->id,
                                'user_id' => $gestores->random()->id,
                                'fecha_checkin' => now()->subHours(rand(1, 24)),
                                'metodo' => rand(0, 1) ? 'qr' : 'manual',
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function shouldConfirm(): bool
    {
        return rand(0, 10) > 3;
    }

    private function randomDni(): string
    {
        return (string) rand(10000000, 99999999);
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
