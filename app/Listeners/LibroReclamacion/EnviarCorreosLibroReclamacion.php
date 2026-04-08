<?php

namespace App\Listeners\LibroReclamacion;

use App\Events\LibroReclamacion\LibroReclamacionRegistrado;
use App\Mail\LibroReclamacion\LibroReclamacionClienteMail;
use App\Mail\LibroReclamacion\LibroReclamacionLegalMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarCorreosLibroReclamacion
{
    public function handle(LibroReclamacionRegistrado $event): void
    {
        $reclamo = $event->reclamo->loadMissing(['proyecto.unidadNegocio']);

        // Prioriza correo legal dedicado; usa pruebas como respaldo.
        $emailLegal = (string) (env('LIBRO_RECLAMACION_EMAIL_LEGAL_TO') ?: env('LIBRO_RECLAMACION_EMAIL_PRUEBAS', ''));

        if ($emailLegal !== '') {
            Mail::to($emailLegal)->send(new LibroReclamacionLegalMail($reclamo));
        }

        if (! empty($reclamo->email) && filter_var($reclamo->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($reclamo->email)->send(new LibroReclamacionClienteMail($reclamo));
        }
    }
}