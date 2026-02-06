<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketEmail;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $admins = User::role(['admin', 'super-admin'])->get();
        $clientes = User::whereDoesntHave('roles')->get(); // O ajusta según tu lógica de clientes

        if ($tickets->isEmpty() || $admins->isEmpty()) {
            return;
        }

        foreach ($tickets as $ticket) {
            // Algunos tickets tienen comunicación por email (40%)
            if (rand(1, 100) <= 40) {
                $emisor = $admins->random();
                $receptor = $ticket->cliente_id ? User::find($ticket->cliente_id) : $clientes->random();

                if ($receptor) {
                    TicketEmail::factory()->count(rand(1, 2))->create([
                        'ticket_id' => $ticket->id,
                        'emisor_id' => $emisor->id,
                        'receptor_id' => $receptor->id,
                        'asunto' => 'Seguimiento de Ticket #' . $ticket->id,
                    ]);
                }
            }
        }
    }
}
