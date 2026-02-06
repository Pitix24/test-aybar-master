<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketMensaje;
use App\Models\TicketArchivo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketArchivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickets = Ticket::all();
        $mensajes = TicketMensaje::all();
        $users = User::all();

        if ($users->isEmpty())
            return;

        // Adjuntar archivos a algunos tickets (directamente)
        foreach ($tickets->random(min($tickets->count(), 10)) as $ticket) {
            TicketArchivo::factory(rand(1, 3))->create([
                'archivable_id' => $ticket->id,
                'archivable_type' => Ticket::class,
                'user_id' => $users->random()->id,
            ]);
        }

        // Adjuntar archivos a algunos mensajes
        if ($mensajes->isNotEmpty()) {
            foreach ($mensajes->random(min($mensajes->count(), 20)) as $mensaje) {
                TicketArchivo::factory(rand(1, 2))->create([
                    'archivable_id' => $mensaje->id,
                    'archivable_type' => TicketMensaje::class,
                    'user_id' => $users->random()->id,
                ]);
            }
        }
    }
}
