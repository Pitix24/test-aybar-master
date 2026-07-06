<?php

namespace App\Mail\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CitaAgendadaGestorLegalMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailCC;

    public function __construct(public ProspectoEntregaFest $prospecto, string $emailCC)
    {
        $this->emailCC = $emailCC;
    }

    public function envelope(): Envelope
    {
        // Manzana / Lote (con detección de reubicación)
        $mz = $this->prospecto->reubicado_manzana ?: $this->prospecto->manzana;
        $lt = $this->prospecto->reubicado_lote    ?: $this->prospecto->lote;
        $mzLt = ($mz || $lt) ? trim("Mz {$mz} Lt {$lt}") : 'Sin Mz/Lt';

        // Fecha amigable
        $fecha = $this->prospecto->fecha_firma
            ? Carbon::parse($this->prospecto->fecha_firma)->format('d/m/Y H:i')
            : 'Sin fecha';

        $asunto = "🗓️ CITA CONTRATO confirmada — {$this->prospecto->nombres} ({$mzLt}) — {$fecha}";

        return new Envelope(
            subject: $asunto,
            cc: $this->emailCC,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.entrega-fest.cita-agendada-gestor-legal',
        );
    }
}
