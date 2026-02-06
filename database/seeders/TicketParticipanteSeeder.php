<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketParticipante;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketParticipanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        foreach ($tickets as $ticket) {
            // Assign between 1 and 3 random participants to each ticket
            $participantCount = rand(1, 3);
            $randomUsers = $users->random(min($participantCount, $users->count()));

            foreach ($randomUsers as $user) {
                TicketParticipante::factory()->create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
