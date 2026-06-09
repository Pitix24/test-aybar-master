<?php

namespace App\Livewire\Web\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaConfirmacion;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Agenda tu Cita de Firma')]
class CitaAgendarPropietario extends Component
{
    // ============ CONFIGURACIÓN DE HORARIOS ============
    public const HORA_INICIO = '10:00';      // Hora mínima (inclusive)
    public const HORA_FIN    = '17:00';      // Hora máxima (inclusive)
    public const INTERVALO_MIN = 30;          // Intervalo en minutos entre slots
    public const DIAS_ANTICIPACION = 3;       // Días mínimos de anticipación
    // =====================================================

    public $slug;
    public $prospecto;
    public $evento;
    public string $direccion_sede = '';

    // Campos del formulario (separados)
    public string $fecha = '';   // YYYY-MM-DD
    public string $hora  = '';   // HH:MM

    // Campo combinado que se guarda en BD
    public string $fecha_firma = '';

    // Datos derivados para la vista
    public string $fechaMinima = '';
    public array  $horariosDisponibles = [];

    public $enviado = false;
    public $mensaje_exito = '';

    public function mount($slug, $propietarioId)
    {
        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto.unidadNegocio'])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;
        $this->direccion_sede = $this->prospecto->proyecto?->unidadNegocio?->direccion ?? '';

        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        if ($this->prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            abort(403, 'Tu contrato aún no ha sido aprobado para agendar firma.');
        }

        // Si ya tiene cita, mostrar confirmación
        if ($this->prospecto->fecha_firma) {
            $this->enviado = true;
            $this->fecha_firma = $this->prospecto->fecha_firma;
            $this->mensaje_exito = 'Ya tienes una cita agendada. Si necesitas cambiarla, comunícate con nosotros.';
            return;
        }

        // Calcular fecha mínima (hoy + N días, saltando fines de semana si cae en sábado/domingo)
        $this->fechaMinima = $this->calcularFechaMinima();

        // Generar slots de horario disponibles
        $this->horariosDisponibles = $this->generarHorarios();
    }

    /**
     * Calcula la fecha mínima permitida sumando los días de anticipación.
     * Si la fecha mínima cae en sábado o domingo, la mueve al lunes siguiente.
     */
    protected function calcularFechaMinima(): string
    {
        $fecha = now()->addDays(self::DIAS_ANTICIPACION);

        // Si cae en sábado (6) o domingo (0/7), mover al lunes
        while ($fecha->isWeekend()) {
            $fecha->addDay();
        }

        return $fecha->format('Y-m-d');
    }

    /**
     * Genera la lista de horarios disponibles entre HORA_INICIO y HORA_FIN
     * en intervalos de INTERVALO_MIN minutos.
     */
    protected function generarHorarios(): array
    {
        $horarios = [];
        $inicio = \Carbon\Carbon::createFromFormat('H:i', self::HORA_INICIO);
        $fin    = \Carbon\Carbon::createFromFormat('H:i', self::HORA_FIN);

        while ($inicio->lte($fin)) {
            $horarios[] = $inicio->format('H:i');
            $inicio->addMinutes(self::INTERVALO_MIN);
        }

        return $horarios;
    }

    protected function rules(): array
    {
        return [
            'fecha' => 'required|date|after_or_equal:' . $this->fechaMinima,
            'hora'  => ['required', 'date_format:H:i', \Illuminate\Validation\Rule::in($this->horariosDisponibles)],
        ];
    }

    protected function messages(): array
    {
        return [
            'fecha.required'        => 'Debes seleccionar la fecha de tu cita.',
            'fecha.date'            => 'La fecha seleccionada no es válida.',
            'fecha.after_or_equal'  => 'La fecha debe tener al menos ' . self::DIAS_ANTICIPACION . ' días de anticipación.',
            'hora.required'         => 'Debes seleccionar el horario de tu cita.',
            'hora.in'               => 'El horario seleccionado no está disponible.',
        ];
    }

    public function save()
    {
        $this->validate();

        // 🛡️ VALIDACIÓN ADICIONAL: día hábil (no permitir sábado/domingo)
        $fechaCarbon = \Carbon\Carbon::parse($this->fecha);
        if ($fechaCarbon->isWeekend()) {
            $this->addError('fecha', 'Solo puedes agendar de Lunes a Viernes.');
            return;
        }

        // 🛡️ VALIDACIÓN ADICIONAL: la combinación fecha+hora no esté en el pasado
        $fechaFirmaCompleta = "{$this->fecha} {$this->hora}:00";
        $fechaCompletaCarbon = \Carbon\Carbon::parse($fechaFirmaCompleta);

        if ($fechaCompletaCarbon->lt(now()->addDays(self::DIAS_ANTICIPACION)->startOfDay())) {
            $this->addError('fecha', 'La fecha y hora seleccionadas no cumplen con la anticipación mínima.');
            return;
        }

        try {
            $this->prospecto->update([
                'fecha_firma' => $fechaFirmaCompleta,
            ]);

            EntregaFestCitaConfirmacion::dispatch($this->prospecto->refresh());

            $this->enviado = true;
            $this->fecha_firma = $fechaFirmaCompleta;
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

    public function render()
    {
        return view('livewire.web.entrega-fest.firma-publica');
    }
}
