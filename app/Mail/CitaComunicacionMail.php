<?php

namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CitaComunicacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public Cita $cita;
    public string $asunto;
    public string $mensaje;
    public array $archivosParaAdjuntar;

    /**
     * @param Cita $cita
     * @param string $asunto
     * @param string $mensaje
     * @param array $archivos CitaArchivo collections or array
     */
    public function __construct(Cita $cita, string $asunto, string $mensaje, array $archivos = [])
    {
        $this->cita = $cita;
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
            view: 'emails.cita-comunicacion',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->archivosParaAdjuntar as $archivo) {
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
