<?php

namespace App\Mail\EntregaFest;

use App\Models\InvitadoEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InstruccionesEventoMail extends Mailable
{
    use Queueable, SerializesModels;

    public InvitadoEntregaFest $invitado;
    public $evento;
    public string $nombrePersona;
    public string $imagenUrl = 'https://plataforma-digital.aybarcorp.com/assets/imagen/construccion-aybar-corp.jpg';

    public function __construct(InvitadoEntregaFest $invitado)
    {
        $invitado->loadMissing(['prospecto.proyecto', 'copropietario.prospecto.proyecto', 'entregaFest']);
        $this->invitado = $invitado;
        $this->evento = $invitado->entregaFest;
        $this->nombrePersona = $invitado->nombre_completo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📋 Instrucciones del Evento - ' . $this->evento->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.instrucciones-evento',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
