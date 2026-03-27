<?php

namespace App\Mail\EntregaFest;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitacionAsistenciaPropietarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prospecto;
    public $evento;
    public $link;

    /**
     * Create a new message instance.
     */
    public function __construct($prospecto)
    {
        $this->prospecto = $prospecto;
        $this->evento = $prospecto->entregaFest;
        $this->link = route('public.entrega-fest.asistencia', [$this->evento->slug, $this->prospecto->id]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirma tu asistencia - ' . $this->evento->nombre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.asistencia-link',
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
