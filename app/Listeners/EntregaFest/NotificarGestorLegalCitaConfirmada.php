<?php

namespace App\Listeners\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaConfirmacion;
use App\Mail\EntregaFest\CitaAgendadaGestorLegalMail;
use App\Models\Area;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificarGestorLegalCitaConfirmada implements ShouldQueue
{
    public function handle(EntregaFestCitaConfirmacion $event): void
    {
        $prospecto = $event->prospecto->load([
            'gestorLegal',
            'entregaFest',
            'proyecto.unidadNegocio',
            'reubicadoProyecto.unidadNegocio',
        ]);

        // Solo procesamos si ya tiene fecha de firma
        if (!$prospecto->fecha_firma) {
            Log::channel('entrega-fest')->info("[CORREO-GESTOR-LEGAL] Saltado para Prospecto #{$prospecto->id}: sin fecha_firma.");
            return;
        }

        // 🆕 Obtener email_buzon del Área Legal (fallback + CC)
        $areaLegal = Area::find(3); // ID 3 corresponde al área LEGAL
        $emailBuzon = $areaLegal?->email_buzon;

        if (!$emailBuzon) {
            // En caso de no encontrarse, log de error para visibilidad y no intentar enviar correo del Area (nombre area ID 3)
            Log::channel('entrega-fest')->error("[CORREO-GESTOR-LEGAL] No se encontró email_buzon del área " . $areaLegal?->nombre, [
                'prospecto_id' => $prospecto->id,
            ]);
            return;
        }

        // 🎯 Definir destinatario principal
        $destinatario = $prospecto->gestorLegal?->email ?? $emailBuzon;
        $tieneGestor  = (bool) $prospecto->gestorLegal;

        // ⚠️ Si no hay gestor, log warning para visibilidad
        if (!$tieneGestor) {
            Log::channel('entrega-fest')->warning(
                '[CITA AGENDADA] Cita confirmada SIN gestor legal asignado — fallback enviado al buzón del área',
                [
                    'prospecto_id'           => $prospecto->id,
                    'cliente'                => $prospecto->nombres,
                    'dni'                    => $prospecto->dni,
                    'fecha_firma'            => $prospecto->fecha_firma?->toDateTimeString(),
                    'evento_id'              => $prospecto->entrega_fest_id,
                    'email_destino_fallback' => $emailBuzon,
                    'motivo'                 => 'Sin gestor_legal_id asignado al momento de la confirmación de cita',
                ]
            );
        }

        try {
            // CC: si hay gestor, copiar al buzón. Si no hay gestor, ya va al buzón como destinatario principal.
            $cc = $tieneGestor ? $emailBuzon : '';

            Mail::to($destinatario)->send(new CitaAgendadaGestorLegalMail($prospecto, $cc));

            Log::channel('entrega-fest')->info('[CORREO-GESTOR-LEGAL] Enviado exitosamente', [
                'prospecto_id'  => $prospecto->id,
                'destinatario'  => $destinatario,
                'cc'            => $cc ?: '(ninguno)',
                'tiene_gestor'  => $tieneGestor,
                'cliente'       => $prospecto->nombres,
            ]);
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error('[CORREO-GESTOR-LEGAL] Error al enviar', [
                'prospecto_id' => $prospecto->id,
                'destinatario' => $destinatario,
                'error'        => $e->getMessage(),
            ]);
        }
    }
}
