<?php

namespace App\Listeners\LibroReclamacion;

use App\Events\LibroReclamacion\LibroReclamacionRegistrado;
use App\Mail\LibroReclamacion\LibroReclamacionClienteMail;
use App\Mail\LibroReclamacion\LibroReclamacionLegalMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviarCorreosLibroReclamacion
{
    public function handle(LibroReclamacionRegistrado $event): void
    {
        $reclamo = $event->reclamo->loadMissing(['proyecto.unidadNegocio']);

        $ticketId = data_get($reclamo, 'ticket_id') ?? data_get($reclamo, 'id');

        $emailLegal = (string) data_get(config('libro_reclamacion', []), 'notifications.to', '');

        if ($emailLegal !== '' && filter_var($emailLegal, FILTER_VALIDATE_EMAIL)) {
            try {
                // Enviar correo legal usando configuración MAIL_RECLAMACION si está disponible
                $mailer = Mail::mailer($this->resolverMailerReclamacion());
                $mailer->to($emailLegal)->send(new LibroReclamacionLegalMail($reclamo));
                
                Log::info('[RECLAMACION][MAIL] Correo legal enviado.', [
                    'ticket' => $ticketId,
                    'codigo_ticket' => data_get($reclamo, 'codigo_ticket'),
                    'destino_legal' => $emailLegal,
                    'mailer' => $this->resolverMailerReclamacion(),
                ]);
            } catch (Throwable $e) {
                Log::error('[RECLAMACION][MAIL] Error al enviar correo legal.', [
                    'ticket' => $ticketId,
                    'codigo_ticket' => data_get($reclamo, 'codigo_ticket'),
                    'destino_legal' => $emailLegal,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $clienteEmail = data_get($reclamo, 'cliente_email', '');
        if (! empty($clienteEmail) && filter_var($clienteEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($clienteEmail)->send(new LibroReclamacionClienteMail($reclamo));
                Log::info('[RECLAMACION][MAIL] Correo cliente enviado.', [
                    'ticket' => $ticketId,
                    'codigo_ticket' => data_get($reclamo, 'codigo_ticket'),
                    'destino_cliente' => $clienteEmail,
                ]);
            } catch (Throwable $e) {
                Log::error('[RECLAMACION][MAIL] Error al enviar correo cliente.', [
                    'ticket' => $ticketId,
                    'codigo_ticket' => data_get($reclamo, 'codigo_ticket'),
                    'destino_cliente' => $clienteEmail,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    /**
     * Resuelve si debe usar el mailer de reclamaciones o el por defecto.
     * Si está configurado el SMTP de reclamaciones, lo usa para el correo legal.
     */
    private function resolverMailerReclamacion(): string
    {
        $hostReclamacion = env('MAIL_HOST_RECLAMACION');
        
        // Si hay configuración de reclamaciones disponible, usarla para correo legal
        if (! empty($hostReclamacion)) {
            return 'reclamacion';
        }

        return config('mail.default', 'smtp');
    }
}