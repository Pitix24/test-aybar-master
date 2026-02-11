<?php

namespace App\Livewire\Cita\Cita;

use App\Models\Cita;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;

#[Layout('layouts.erp.layout-erp')]
class CitaCalendario extends Component
{
    public $vista = 'mes';
    public $fechaActual;
    public $eventos = [];

    public function mount()
    {
        $this->fechaActual = Carbon::now();
        $this->loadEventos();
    }

    public function cambiarVista($vista)
    {
        $this->vista = $vista;
        $this->loadEventos();
    }

    public function navegar($valor)
    {
        match ($this->vista) {
            'mes' => $this->fechaActual->addMonths($valor),
            'semana' => $this->fechaActual->addWeeks($valor),
            'dia' => $this->fechaActual->addDays($valor),
            'anio' => $this->fechaActual->addYears($valor),
        };

        $this->loadEventos();
    }

    public function irHoy()
    {
        $this->fechaActual = Carbon::now();
        $this->loadEventos();
    }

    public function loadEventos()
    {
        $inicio = match ($this->vista) {
            'mes' => $this->fechaActual->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY),
            'semana' => $this->fechaActual->copy()->startOfWeek(Carbon::MONDAY),
            'dia' => $this->fechaActual->copy()->startOfDay(),
            default => $this->fechaActual->copy()->startOfMonth(),
        };

        $fin = match ($this->vista) {
            'mes' => $this->fechaActual->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY),
            'semana' => $this->fechaActual->copy()->endOfWeek(Carbon::SUNDAY),
            'dia' => $this->fechaActual->copy()->endOfDay(),
            default => $this->fechaActual->copy()->endOfMonth(),
        };

        $this->eventos = Cita::with(['cliente', 'sede', 'motivo', 'area', 'estado'])
            ->whereBetween('fecha_inicio', [$inicio, $fin])
            ->orderBy('fecha_inicio')
            ->get()
            ->map(fn($cita) => [
                'id' => $cita->id,
                'title' => $cita->motivo?->nombre ?? 'Sin Motivo',
                'area' => $cita->area?->nombre ?? 'N/A',
                'color' => $cita->area?->color ?? '#64748b',
                'cliente' => $cita->nombres,
                'sede' => $cita->sede?->nombre,
                'estado' => $cita->estado?->nombre,
                'date' => $cita->fecha_inicio?->toDateString(),
                'time' => $cita->fecha_inicio?->format('H:i'),
                'end_time' => $cita->fecha_fin?->format('H:i'),
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.cita.cita.cita-calendario');
    }
}
