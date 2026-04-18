<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class TicketService
{
    /**
     * Calcula la fecha de vencimiento basada en horas de solución y horario laboral.
     *
     * @param Carbon $fechaInicio
     * @param int $horasSolucion
     * @return Carbon
     */
    public static function calcularFechaVencimiento(\Carbon\CarbonInterface $fechaInicio, $horasSolucion)
    {
        $horarios = Config::get('horarios.laboral');
        $fechaVencimiento = $fechaInicio->copy();
        $horasRestantes = (float) $horasSolucion;

        // Máximo 30 iteraciones para evitar bucles infinitos por error de config
        $intentos = 0;
        while ($horasRestantes > 0 && $intentos < 30) {
            $intentos++;
            $diaSemana = $fechaVencimiento->dayOfWeek; // 0 para Domingo, 1 para Lunes, etc.
            $horarioHoy = $horarios[$diaSemana] ?? null;

            // 1. Si no es día laboral, saltar al día siguiente 00:00
            if (!$horarioHoy) {
                $fechaVencimiento = $fechaVencimiento->addDay()->startOfDay();
                continue;
            }

            $inicioJornada = $fechaVencimiento->copy()->setTimeFromTimeString($horarioHoy['start']);
            $finJornada = $fechaVencimiento->copy()->setTimeFromTimeString($horarioHoy['end']);

            // 2. Si estamos antes de que empiece la jornada, saltar al inicio
            if ($fechaVencimiento->lt($inicioJornada)) {
                $fechaVencimiento = $inicioJornada;
            }

            // 3. Si ya terminó la jornada de hoy, saltar al día siguiente 00:00
            if ($fechaVencimiento->gte($finJornada)) {
                $fechaVencimiento = $fechaVencimiento->addDay()->startOfDay();
                continue;
            }

            // 4. Calcular horas disponibles hoy desde el punto actual
            $segundosDisponiblesHoy = $fechaVencimiento->diffInSeconds($finJornada);
            $horasDisponiblesHoy = $segundosDisponiblesHoy / 3600;

            if ($horasRestantes <= $horasDisponiblesHoy) {
                // Se termina dentro de la jornada de hoy
                $fechaVencimiento = $fechaVencimiento->addSeconds(round($horasRestantes * 3600));
                $horasRestantes = 0;
            } else {
                // Se agota el tiempo de hoy, pasar al día siguiente
                $horasRestantes -= $horasDisponiblesHoy;
                $fechaVencimiento = $fechaVencimiento->addDay()->startOfDay();
            }
        }

        return $fechaVencimiento;
    }

    /**
     * Verifica si existe un ticket con los mismos datos base.
     */
    public static function existeTicketSimilar($dni, $loteId, $tipoSolicitudId, $subTipoSolicitudId)
    {
        return \App\Models\Ticket::where('dni', $dni)
            ->where('tipo_solicitud_id', $tipoSolicitudId)
            ->where('sub_tipo_solicitud_id', $subTipoSolicitudId)
            ->whereJsonContains('lotes', ['id' => (int)$loteId])
            ->whereNotIn('estado_ticket_id', [
                \App\Models\EstadoTicket::id(\App\Models\EstadoTicket::CERRADO)
            ])
            ->exists();
    }
}
