<?php

namespace App\Listeners;

use App\Events\TicketCreado;
use App\Mail\TicketCreadoMail;
use Illuminate\Support\Facades\Mail;

class EnviarCorreoTicketCreado
{
    public function handle(TicketCreado $event): void
    {
        $area = $event->ticket->area;

        if (!$area?->email_buzon) {
            return;
        }

        Mail::to($area->email_buzon)
            ->send(new TicketCreadoMail($event->ticket));
    }
}
