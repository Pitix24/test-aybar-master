<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TicketComunicacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public string $asunto;
    public string $mensaje;
    public array $archivosParaAdjuntar;

    /**
     * @param Ticket $ticket
     * @param string $asunto
     * @param string $mensaje
     * @param array $archivos TicketArchivo collections or array
     */
    public function __construct(Ticket $ticket, string $asunto, string $mensaje, array $archivos = [])
    {
        $this->ticket = $ticket;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->archivosParaAdjuntar = $archivos;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-comunicacion',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->archivosParaAdjuntar as $archivo) {
            // Manejar tanto objetos como arrays si fuera necesario
            $path = is_object($archivo) ? $archivo->path : $archivo['path'];
            $nombre = is_object($archivo) ? $archivo->nombre_original : $archivo['nombre_original'];
            $mime = is_object($archivo) ? $archivo->mime_type : $archivo['mime_type'];

            if (Storage::disk('public')->exists($path)) {
                $attachments[] = Attachment::fromPath(Storage::disk('public')->path($path))
                    ->as($nombre)
                    ->withMime($mime);
            }
        }

        return $attachments;
    }
}
