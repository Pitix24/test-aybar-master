<?php

namespace App\Listeners;

use App\Events\EntregaFestFirmaRecordatorio;
use App\Mail\EntregaFest\FirmaConfirmacionMail;
use App\Services\WhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarRecordatorioFirma implements ShouldQueue
{
    public function __construct(private WhatsappService $whatsapp)
    {
        //
    }

    public function handle(EntregaFestFirmaRecordatorio $event): void
    {
        $prospecto = $event->prospecto;
        $prospecto->load(['entregaFest', 'proyecto']);

        // 1. Correo Electrónico
        if ($prospecto->email) {
            try {
                Mail::to($prospecto->email)->send(new FirmaConfirmacionMail($prospecto));
            } catch (\Exception $e) {
                Log::error("[RECORDATORIO-FIRMA] Error enviando correo a {$prospecto->email}: " . $e->getMessage());
            }
        }

        // 2. WhatsApp
        if ($prospecto->celular) {
            try {
                $fechaFormateada = Carbon::parse($prospecto->fecha_firma)
                    ->locale('es')
                    ->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm');

                $celRaw = preg_replace('/\D/', '', $prospecto->celular);
                $celular = strlen($celRaw) === 9 ? '51' . $celRaw : $celRaw;

                $mensaje = "Hola *{$prospecto->nombres}*, te recordamos tu cita para la firma de contrato del proyecto *{$prospecto->proyecto?->nombre}*.\n\n"
                    . "📅 *Fecha:* " . ucfirst($fechaFormateada) . "\n\n"
                    . "⚠️ *Importante:*\n"
                    . "• Presentarse puntualmente.\n"
                    . "• Llevar DNI original físico.\n\n"
                    . "¡Te esperamos!";

                $this->whatsapp->sendText($celular, $mensaje);
            } catch (\Exception $e) {
                Log::error("[RECORDATORIO-FIRMA] Error enviando WhatsApp a {$prospecto->celular}: " . $e->getMessage());
            }
        }
    }
}
