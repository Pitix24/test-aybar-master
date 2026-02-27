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
    public $evento;
    // Datos resueltos desde los accessors del invitado (funciona para titular Y copropietario)
    public $nombrePersona;
    public $proyecto;
    public $lote;
    public $manzana;

    public function __construct($invitado)
    {
        $this->invitado = $invitado->loadMissing(['prospecto.proyecto', 'copropietario.prospecto.proyecto']);
        $this->evento = $invitado->entregaFest;

        // Accessor nombre_completo ya maneja titular/copropietario
        $this->nombrePersona = $invitado->nombre_completo;

        // Resolver proyecto/lote/manzana sin importar el tipo
        $this->proyecto = $invitado->prospecto?->proyecto?->nombre
            ?? $invitado->copropietario?->prospecto?->proyecto?->nombre
            ?? 'N/A';
        $this->lote = $invitado->lote ?? '—';
        $this->manzana = $invitado->manzana ?? '—';
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
