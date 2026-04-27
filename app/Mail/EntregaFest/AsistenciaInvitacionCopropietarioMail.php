<?php

namespace App\Mail\EntregaFest;

use App\Models\CopropietarioEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AsistenciaInvitacionCopropietarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public CopropietarioEntregaFest $copropietario;
    public $evento;
    public $link;
    public $plantilla;

    public function __construct(CopropietarioEntregaFest $copropietario, $plantilla = null)
    {
        $this->copropietario = $copropietario;
        $this->plantilla = $plantilla;
        $this->evento = $copropietario->prospecto->entregaFest;
        // Link a la página PROPIA del copropietario
        $this->link = route('entrega-fest.asistencia-invitacion.copropietario', [
            'slug' => $this->evento->slug,
            'copropietarioId' => $copropietario->id,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirma tu asistencia - ' . $this->evento->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.asistencia-invitacion-copropietario',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
