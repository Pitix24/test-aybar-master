<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Ticket;
use App\Models\TicketDerivado;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketDerivadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $areas = Area::all();
        $users = User::all();

        if ($tickets->isEmpty() || $areas->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Solo derivamos algunos tickets (30%)
        $ticketsADerivar = $tickets->random((int) ($tickets->count() * 0.3));

        foreach ($ticketsADerivar as $ticket) {
            $areaOrigen = $ticket->area_id ?? $areas->random()->id;
            $areaDestino = $areas->where('id', '!=', $areaOrigen)->random()->id ?? $areas->random()->id;

            TicketDerivado::create([
                'ticket_id' => $ticket->id,
                'de_area_id' => $areaOrigen,
                'a_area_id' => $areaDestino,
                'usuario_deriva_id' => $users->random()->id,
                'usuario_recibe_id' => $users->random()->id,
                'motivo' => 'Derivación de prueba por flujo de trabajo.',
            ]);
        }
    }
}
