<?php

namespace App\Mail\EntregaFest;

use App\Models\ProspectoEntregaFest;
use App\Models\Area;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContratoPreliminarMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProspectoEntregaFest $prospecto;
    protected $areaEmail;
    public $evento;
    public $link;

    public function __construct(ProspectoEntregaFest $prospecto)
    {
        $this->prospecto = $prospecto;
        $this->areaEmail = Area::find(3)?->email_buzon;
        $this->evento = $prospecto->entregaFest;
        $this->link = route('entrega-fest.cita-agendar.propietario', [
            'slug' => $this->evento->slug,
            'propietarioId' => $prospecto->id,
        ]);
    }

    public function envelope(): Envelope
    {
        $copiaOculta = [];
        if ($this->areaEmail) {
            $copiaOculta[] = new Address($this->areaEmail, 'Área de Control');
        }

        return new Envelope(
            subject: '📅 Contrato Preliminar - ' . $this->areaEmail . ' - ' . $this->evento->nombre,
            bcc: $copiaOculta,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.contrato-preliminar',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
