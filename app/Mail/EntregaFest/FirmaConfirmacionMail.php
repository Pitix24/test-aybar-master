<?php

namespace App\Mail\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FirmaConfirmacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProspectoEntregaFest $prospecto;
    public $evento;
    public string $fechaFormateada;

    public function __construct(ProspectoEntregaFest $prospecto)
    {
        $this->prospecto = $prospecto;
        $this->evento = $prospecto->entregaFest;

        $this->fechaFormateada = \Carbon\Carbon::parse($prospecto->fecha_firma)
            ->locale('es')
            ->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📅 Cita de Firma Agendada - ' . $this->evento->nombre,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.firma-confirmacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
