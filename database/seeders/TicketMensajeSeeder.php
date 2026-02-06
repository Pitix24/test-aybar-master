<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketMensaje;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketMensajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $users = User::where('rol', 'admin')->get();

        if ($tickets->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($tickets as $ticket) {
            // Cada ticket tendrá entre 2 y 5 mensajes
            TicketMensaje::factory(rand(2, 5))->create([
                'ticket_id' => $ticket->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
