<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketHistorial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketHistorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $users = User::all();

        if ($tickets->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($tickets as $ticket) {
            // Cada ticket tiene al menos un historial de creación
            TicketHistorial::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->created_by ?? $users->random()->id,
                'accion' => 'CREADO',
                'detalle' => 'Ticket creado en el sistema.',
                'created_at' => $ticket->created_at,
            ]);

            // Algunos tickets tienen más historial
            if (rand(0, 1)) {
                TicketHistorial::factory()->count(rand(1, 3))->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
    }
}
