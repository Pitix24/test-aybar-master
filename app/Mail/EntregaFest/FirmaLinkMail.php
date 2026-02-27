<?php

namespace App\Mail\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FirmaLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProspectoEntregaFest $prospecto;
    public $evento;
    public $link;

    public function __construct(ProspectoEntregaFest $prospecto)
    {
        $this->prospecto = $prospecto;
        $this->evento = $prospecto->entregaFest;
        $this->link = route('public.entrega-fest.firma', [
            $this->evento->slug,
            $prospecto->id,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📅 Agenda tu Cita de Firma - ' . $this->evento->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.firma-link',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
