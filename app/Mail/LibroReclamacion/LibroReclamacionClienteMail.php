<?php

namespace App\Mail\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion as LibroReclamacionModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LibroReclamacionClienteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LibroReclamacionModel $reclamo)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hoja de Reclamación N° ' . ($this->reclamo->codigo_ticket ?? 'SIN-CODIGO'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.libro-reclamacion.cliente-confirmacion',
        );
    }

    public function attachments(): array
    {
        $codigo = $this->reclamo->codigo_ticket ?? 'SIN-CODIGO';
        $reclamo = $this->reclamo;

        return [
            Attachment::fromData(
                fn () => Pdf::loadView('emails.libro-reclamacion.cliente-confirmacion', ['reclamo' => $reclamo])->output(),
                'Hoja de Reclamacion ' . $codigo . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}