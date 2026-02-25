<?php

namespace App\Mail\EntregaFest;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketAsistenciaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitado;
    public $prospecto;
    public $evento;

    /**
     * Create a new message instance.
     */
    public function __construct($invitado)
    {
        $this->invitado = $invitado;
        $this->prospecto = $invitado->prospecto;
        $this->evento = $invitado->entregaFest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎫 Tu Pase de Entrada - ' . $this->evento->nombre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.ticket-asistencia',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
