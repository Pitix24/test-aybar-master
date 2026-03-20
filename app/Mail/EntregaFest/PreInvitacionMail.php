<?php

namespace App\Mail\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreInvitacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prospecto;
    public $evento;
    public $link;

    /**
     * Create a new message instance.
     */
    public function __construct(ProspectoEntregaFest $prospecto)
    {
        $this->prospecto = $prospecto;
        $this->evento = $prospecto->entregaFest;
        // El link lleva a la página de asistencia donde el usuario confirmará su interés
        $this->link = route('public.entrega-fest.asistencia', [
            'slug' => $this->evento->slug,
            'id' => $this->prospecto->id
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pre-invitación: ' . $this->evento->nombre,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.preinvitacion',
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
