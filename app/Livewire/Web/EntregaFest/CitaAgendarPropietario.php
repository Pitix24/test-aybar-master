<?php

namespace App\Livewire\Web\EntregaFest;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Events\EntregaFest\EntregaFestCitaConfirmacion;
use App\Models\ProspectoEntregaFest;
use App\Support\RedirigeSiEventoConcluido;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Agenda tu Cita de Firma')]
class CitaAgendarPropietario extends Component
{
    use RedirigeSiEventoConcluido;

    // ============ CONFIGURACIÓN DE HORARIOS ============
    public const HORA_INICIO       = '10:00';
    public const HORA_FIN          = '17:00';
    public const INTERVALO_MIN     = 30;
    public const DIAS_ANTICIPACION = 4;
    public const CUPO_POR_HORARIO  = 2;
    // ====================================================

    public $slug;
    public $prospecto;
    public $evento;
    public string $direccion_sede = '';

    public string $fecha = '';
    public string $hora  = '';
    public string $fecha_firma = '';

    public string $fechaMinima = '';
    public array  $horariosDisponibles = [];

    public $enviado = false;
    public $mensaje_exito = '';

    // ============================================================
    //                          MOUNT
    // ============================================================

    public function mount($slug, $propietarioId)
    {
        $this->prospecto = ProspectoEntregaFest::with([
                'entregaFest',
                'proyecto.unidadNegocio',
                'reubicadoProyecto.unidadNegocio', // 🆕 AGREGAR
            ])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;

        // 🆕 La sede ahora se calcula con el proyecto activo (reubicado u original)
        $this->direccion_sede = $this->proyectoActivo?->unidadNegocio?->direccion ?? '';

        if ($this->evento->slug !== $slug) abort(404);

        if ($this->prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            abort(403, 'Tu contrato aún no ha sido aprobado para agendar firma.');
        }

        if ($this->prospecto->fecha_firma) {
            $this->enviado       = true;
            $this->fecha_firma   = $this->prospecto->fecha_firma;
            $this->mensaje_exito = 'Ya tienes una cita agendada. Si necesitas cambiarla, comunícate con nosotros.';
            return;
        }

        $this->fechaMinima         = $this->calcularFechaMinima();
        $this->horariosDisponibles = $this->generarHorarios();
    }

    // ============================================================
    //                       HELPERS DE HORARIO
    // ============================================================

    protected function calcularFechaMinima(): string
    {
        // ⚠️ Importante: con CarbonImmutable, addDay() NO muta el objeto.
        //    Hay que reasignar siempre.
        $fecha = now()->addDays(self::DIAS_ANTICIPACION);

        $maxIteraciones = 10; // Safety guard contra bucles
        $i = 0;

        while ($fecha->isWeekend() && $i < $maxIteraciones) {
            $fecha = $fecha->addDay(); // 🔧 REASIGNACIÓN obligatoria con CarbonImmutable
            $i++;
        }

        return $fecha->format('Y-m-d');
    }

    protected function generarHorarios(?string $fecha = null): array
    {
        $ocupacion = $fecha ? $this->obtenerOcupacionDelDia($fecha) : [];

        $horarios = [];
        $inicio = \Carbon\Carbon::createFromFormat('H:i', self::HORA_INICIO);
        $fin    = \Carbon\Carbon::createFromFormat('H:i', self::HORA_FIN);

        $safety = 0;
        while ($inicio->lte($fin) && $safety < 100) {
            $horaStr  = $inicio->format('H:i');
            $ocupados = $ocupacion[$horaStr] ?? 0;

            $horarios[] = [
                'hora'       => $horaStr,
                'ocupados'   => $ocupados,
                'disponible' => $ocupados < self::CUPO_POR_HORARIO,
            ];

            $inicio = $inicio->addMinutes(self::INTERVALO_MIN); // 🔧 REASIGNACIÓN
            $safety++;
        }

        return $horarios;
    }

    /**
     * 🆕 Optimizado: el conteo se hace en MySQL, no en PHP.
     */
    protected function obtenerOcupacionDelDia(string $fecha): array
    {
        return ProspectoEntregaFest::query()
            ->whereDate('fecha_firma', $fecha)
            ->where('id', '!=', $this->prospecto->id)
            ->selectRaw("DATE_FORMAT(fecha_firma, '%H:%i') as hora, COUNT(*) as total")
            ->groupBy('hora')
            ->pluck('total', 'hora')
            ->toArray();
    }

    // ============================================================
    //                          REACTIVOS
    // ============================================================

    public function updatedFecha($value): void
    {
        if (!$value) {
            $this->horariosDisponibles = $this->generarHorarios();
            return;
        }

        $this->horariosDisponibles = $this->generarHorarios($value);

        if ($this->hora) {
            $slot = collect($this->horariosDisponibles)->firstWhere('hora', $this->hora);
            if (!$slot || !$slot['disponible']) {
                $this->hora = '';
            }
        }
    }

    // ============================================================
    //                         VALIDACIÓN
    // ============================================================

    protected function rules(): array
    {
        $horariosValidos = collect($this->horariosDisponibles)
            ->where('disponible', true)
            ->pluck('hora')
            ->toArray();

        return [
            'fecha' => 'required|date|after_or_equal:' . $this->fechaMinima,
            'hora'  => ['required', 'date_format:H:i', Rule::in($horariosValidos)],
        ];
    }

    protected function messages(): array
    {
        return [
            'fecha.required'       => 'Debes seleccionar la fecha de tu cita.',
            'fecha.date'           => 'La fecha seleccionada no es válida.',
            'fecha.after_or_equal' => 'La fecha debe tener al menos ' . self::DIAS_ANTICIPACION . ' días hábiles de anticipación.',
            'hora.required'        => 'Debes seleccionar el horario de tu cita.',
            'hora.in'              => 'El horario seleccionado no está disponible.',
        ];
    }

    // ============================================================
    //                            SAVE
    // ============================================================

    public function save()
    {
        $this->validate();

        // Validación: día hábil
        if (Carbon::parse($this->fecha)->isWeekend()) {
            $this->addError('fecha', 'Solo puedes agendar de Lunes a Viernes.');
            return;
        }

        // 🆕 Validación de anticipación consistente con fechaMinima (ajustada por weekends)
        if ($this->fecha < $this->fechaMinima) {
            $this->addError('fecha', 'La fecha seleccionada no cumple con la anticipación mínima requerida.');
            return;
        }

        $fechaFirmaCompleta = "{$this->fecha} {$this->hora}:00";

        // Re-validación de cupo (defensa contra concurrencia)
        $ocupados = ProspectoEntregaFest::whereDate('fecha_firma', $this->fecha)
            ->whereTime('fecha_firma', "{$this->hora}:00")
            ->where('id', '!=', $this->prospecto->id)
            ->count();

        if ($ocupados >= self::CUPO_POR_HORARIO) {
            $this->horariosDisponibles = $this->generarHorarios($this->fecha);
            $this->hora = '';
            $this->addError('hora',
                'Lo sentimos, este horario acaba de ser tomado por otro cliente. ' .
                'Por favor selecciona otro horario disponible.');
            return;
        }

        try {
            $this->prospecto->update(['fecha_firma' => $fechaFirmaCompleta]);

            EntregaFestCitaConfirmacion::dispatch($this->prospecto->refresh());

            $this->enviado       = true;
            $this->fecha_firma   = $fechaFirmaCompleta;
            $this->mensaje_exito = '¡Listo! Tu cita de firma ha sido agendada con éxito. ' .
                'Te hemos enviado los detalles de confirmación a tu correo y WhatsApp.';

            Log::info('[CITA AGENDAR PUBLICA] Fecha agendada', [
                'prospecto_id' => $this->prospecto->id,
                'fecha_firma'  => $fechaFirmaCompleta,
            ]);
        } catch (\Exception $e) {
            Log::error('[CITA AGENDAR PUBLICA] Error: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar tu cita. Por favor intenta más tarde.');
        }
    }

    // ============================================================
    //                    UBICACIÓN DEL LOTE
    // ============================================================

    /**
     * Indica si el prospecto fue reubicado a otra manzana/lote.
     */
    #[Computed]
    public function fueReubicado(): bool
    {
        return !empty($this->prospecto->reubicado_manzana)
            || !empty($this->prospecto->reubicado_lote);
    }

    /**
     * Manzana a mostrar al cliente: la reubicada si existe, o la original.
     */
    #[Computed]
    public function manzanaActiva(): ?string
    {
        return $this->fueReubicado
            ? $this->prospecto->reubicado_manzana
            : $this->prospecto->manzana;
    }

    /**
     * Lote a mostrar al cliente: el reubicado si existe, o el original.
     */
    #[Computed]
    public function loteActivo(): ?string
    {
        return $this->fueReubicado
            ? $this->prospecto->reubicado_lote
            : $this->prospecto->lote;
    }

    /**
     * Proyecto a mostrar: el reubicado si existe, o el original.
     * (Por consistencia, ya que si fue reubicado probablemente cambió de proyecto también.)
     */
    #[Computed]
    public function proyectoActivo()
    {
        return $this->fueReubicado && $this->prospecto->reubicadoProyecto
            ? $this->prospecto->reubicadoProyecto
            : $this->prospecto->proyecto;
    }

    // ============================================================
    //                           RENDER
    // ============================================================

    public function render()
    {
        return view('livewire.web.entrega-fest.firma-publica');
    }
}
