<?php

namespace App\Mail\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion as LibroReclamacionModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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
            subject: 'Confirmacion de reclamo - ' . ($this->reclamo->codigo_ticket ?? 'SIN-CODIGO'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.libro-reclamacion.cliente-confirmacion',
        );
    }
}