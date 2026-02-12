<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public string $url;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->url = route('admin.ticket.vista.editar', $ticket->id);
    }

    public function build()
    {
        return $this
            ->subject('Nuevo ticket creado #' . $this->ticket->id)
            ->view('emails.ticket-creado');
    }
}
