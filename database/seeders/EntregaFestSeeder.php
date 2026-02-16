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
        $admin = User::first() ?? User::factory()->create(['name' => 'Admin']);
        $gestores = User::factory()->count(3)->create();

        // Crear una Unidad de Negocio y un Proyecto
        $unidad = UnidadNegocio::first() ?? UnidadNegocio::factory()->create(['nombre' => 'Inmobiliaria Aybar']);
        $proyecto = Proyecto::first() ?? Proyecto::factory()->create(['nombre' => 'Altos de la Valle', 'unidad_negocio_id' => $unidad->id]);

        // 1. Crear Evento Principal
        $evento = EntregaFest::create([
            'unidad_negocio_id' => $unidad->id,
            'proyecto_id' => $proyecto->id,
            'cliente_id' => Cliente::first()?->id ?? Cliente::factory()->create()->id,
            'user_id' => $admin->id,
            'nombre' => 'Gran Entrega Fest Verano 2026',
            'descripcion' => 'Evento masivo de entrega de lotes y festival para propietarios.',
            'codigo' => 'EF-2026-001',
            'fecha_entrega' => now()->addMonths(2),
            'activo' => true,
        ]);

        // 2. Crear Prospectos (Mezcla de estados)
        $prospectos = ProspectoEntregaFest::factory()
            ->count(50)
            ->create([
                'entrega_fest_id' => $evento->id,
                'user_id' => $gestores->random()->id
            ]);

        // 3. Convertir a Invitados (Solo los aprobados)
        $prospectosAprobados = $prospectos->where('estado', 'aprobado');

        foreach ($prospectosAprobados as $prospecto) {
            $invitado = InvitadoEntregaFest::create([
                'entrega_fest_id' => $evento->id,
                'prospecto_entrega_fest_id' => $prospecto->id,
                'codigo_invitado' => 'QR-' . strtoupper(bin2hex(random_bytes(4))),
                'cantidad_acompanantes_permitidos' => rand(1, 3),
                'confirmado' => (bool) rand(0, 1),
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
                'canal' => rand(0, 1) ? 'whatsapp' : 'correo',
                'estado' => 'enviado',
                'detalle' => 'Invitación enviada correctamente.',
                'user_id' => $gestores->random()->id,
                'fecha_envio' => now()->subDays(rand(1, 5)),
            ]);

            // 6. Simular Asistencia para algunos (si el evento ya hubiera pasado o para pruebas)
            if ($invitado->confirmado && rand(0, 1)) {
                AsistenciaEntregaFest::create([
                    'invitado_entrega_fest_id' => $invitado->id,
                    'user_id' => $gestores->random()->id,
                    'fecha_checkin' => now()->subHours(rand(1, 48)),
                    'metodo' => rand(0, 1) ? 'qr' : 'dni',
                ]);
            }
        }
    }
}
