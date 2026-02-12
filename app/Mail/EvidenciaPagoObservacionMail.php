<?php

namespace App\Mail;

use App\Models\SolicitudEvidenciaPago;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EvidenciaPagoObservacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public SolicitudEvidenciaPago $solicitud;
    public string $email;
    public string $mensaje;

    public function __construct($emailDestino, SolicitudEvidenciaPago $solicitud, $mensaje)
    {
        $this->email = $emailDestino;
        $this->solicitud = $solicitud;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this
            ->subject('Solicitud de Evidencia de Pago - Aybar Corp')
            ->view('emails.evidencia-pago-observacion');
    }
}
