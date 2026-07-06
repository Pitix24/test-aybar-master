<?php

namespace App\Mail\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitaConfirmacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProspectoEntregaFest $prospecto;
    public $evento;
    public string $fechaFormateada;
    public ?string $sedeNombre;
    public ?string $direccionSede;

    public function __construct(ProspectoEntregaFest $prospecto)
    {
        $this->prospecto = $prospecto->loadMissing(['proyecto.unidadNegocio']);
        $this->evento = $prospecto->entregaFest;

        $this->fechaFormateada = \Carbon\Carbon::parse($prospecto->fecha_firma)
            ->locale('es')
            ->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm');

        $this->sedeNombre = $this->prospecto->proyecto?->unidadNegocio?->nombre;
        $this->direccionSede = $this->prospecto->proyecto?->unidadNegocio?->direccion;
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
            view: 'emails.entrega-fest.cita-confirmacion',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
