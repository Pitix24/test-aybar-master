<?php

namespace App\Mail\EntregaFest;

use App\Models\CopropietarioEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreInvitacionCopropietarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $copropietario;
    public $evento;
    public $link;

    /**
     * Create a new message instance.
     */
    public function __construct(CopropietarioEntregaFest $copropietario)
    {
        $this->copropietario = $copropietario;
        $this->evento = $copropietario->prospecto->entregaFest;
        $this->link = route('public.entrega-fest.pre-invitacion.copropietario', [
            'slug' => $this->evento->slug,
            'copropietarioId' => $this->copropietario->id
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pre-invitación: ' . $this->evento->nombre . ' (Copropietario)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.preinvitacion-copropietario',
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
