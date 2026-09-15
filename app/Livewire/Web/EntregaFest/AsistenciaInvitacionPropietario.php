<?php

namespace App\Livewire\Web\EntregaFest;

use App\Support\RedirigeSiEventoConcluido;
use App\Support\RedirigeSiAforoLleno;
use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Formulario de Asistencia - Entrega Fest')]
class AsistenciaInvitacionPropietario extends Component
{
    public $slug;
    public $id;
    public $prospecto;
    public $evento;

    // Form fields
    public $asistira = 'si';
    public $cantidad_acompanantes = 0;
    public $transporte = 'bus';
    public $observaciones = '';
    public $limite_acompanantes = 2;
    public $cupo_bus_disponible = null; // null = sin límite configurado

    // Datos del acompañante
    public $acompanantes = [];

    public $enviado = false;
    public $mensaje_exito = '';
    public $codigo_invitado = '';

    // Para mostrar info adicional en el view
    use RedirigeSiEventoConcluido, RedirigeSiAforoLleno;

    public function mount($slug, $propietarioId)
    {
        $this->slug = $slug;
        $this->id = $propietarioId;

        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto', 'invitado'])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;

        $this->limite_acompanantes = $this->evento->limiteAcompanantes();
        $this->cupo_bus_disponible = $this->evento->cupoBusDisponible();

        // 🛑 Si el evento ya se realizó → redirigir
        if ($redir = $this->redirigirSiConcluido($this->evento)) return $redir;

        // 🛑 NUEVA CONDICIÓN: Verificar aforo (Solo validamos aforo si el cliente AÚN NO HA RESPONDIDO)
        // Si el cliente ya había confirmado, debe poder entrar a ver su respuesta normal.
        if (is_null($this->prospecto->invitacion_confirmada)) {
            // Mandamos a validar. Le pasamos 250 como límite.
            if ($redirLleno = $this->redirigirSiLleno($this->evento, 1)) return $redirLleno;

            $this->avisarSiBusNoAlcanza();
        }

        // Validar slug
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // Si ya respondió (confirmó o rechazó), no permitir volver a llenar
        if (!is_null($this->prospecto->invitacion_confirmada)) {
            $this->enviado = true;
            $this->mensaje_exito = 'Ya hemos registrado tu respuesta anteriormente. ¡Muchas gracias!';
            $this->codigo_invitado = $this->prospecto->invitado?->codigo_invitado;
            $this->cantidad_acompanantes = $this->prospecto->invitado?->cantidad_acompanantes_permitidos ?? 0;
            $this->transporte = strtolower($this->prospecto->invitado?->transporte ?? 'bus');
            $this->asistira = $this->prospecto->invitacion_confirmada ? 'si' : 'no';
        }

        // Si no está aprobado en backoffice, no debería estar aquí (opcional)
        /*if ($this->prospecto->estado_backoffice !== 'CONFORME') {
            abort(403, 'Tu evaluación aún no ha sido aprobada.');
        }*/
    }

    protected function rules()
    {
        $valoresConAcompanantes = implode(',', range(1, max($this->limite_acompanantes, 1)));

        return [
            'asistira' => 'required|in:si,no',
            'cantidad_acompanantes' => 'required_if:asistira,si|integer|min:0|max:'.$this->limite_acompanantes,
            'transporte' => 'required_if:asistira,si|in:bus,propio',
            'observaciones' => 'nullable|string|max:500',

            'acompanantes.*.dni' => "required_if:cantidad_acompanantes,{$valoresConAcompanantes}|nullable|string|max:15",
            'acompanantes.*.nombres' => "required_if:cantidad_acompanantes,{$valoresConAcompanantes}|nullable|string|max:255",
            'acompanantes.*.email' => 'nullable|email|max:255',
            'acompanantes.*.celular' => 'nullable|string|max:20',
        ];
    }

    public function updatedCantidadAcompanantes($value)
    {
        // Limpiamos el array y creamos campos vacíos para la cantidad seleccionada
        $this->acompanantes = [];

        for ($i = 0; $i < (int)$value; $i++) {
            $this->acompanantes[] = [
                'dni' => '',
                'nombres' => '',
                'email' => '',
                'celular' => ''
            ];
        }

        $this->avisarSiBusNoAlcanza();
    }

    public function updatedTransporte($value)
    {
        $this->avisarSiBusNoAlcanza();
    }

    /**
     * La opción "bus" ya viene deshabilitada en el <select> cuando no hay cupo, pero esto
     * corrige el estado igual (carga inicial, o si el navegador no respeta el disabled) y
     * explica por qué con una notificación.
     */
    protected function avisarSiBusNoAlcanza(): void
    {
        if ($this->transporte !== 'bus' || $this->cupo_bus_disponible === null) {
            return;
        }

        $personasEnBus = 1 + (int) $this->cantidad_acompanantes;

        if ($personasEnBus > $this->cupo_bus_disponible) {
            $this->transporte = 'propio';

            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Cupos Completos',
                'text' => 'Se ha alcanzado el límite de Cupos disponibles para la modalidad BUS AYBAR. Para continuar con tu registro, por favor, selecciona Movilidad Propia como modalidad de traslado.',
                'showConfirmButton' => true,
            ]);
        }
    }

    public function save()
    {
        $this->validate();

        // Doble check
        if (!is_null($this->prospecto->invitacion_confirmada)) {
            return;
        }

        try {
            DB::beginTransaction();

            $confirmado = ($this->asistira === 'si');

            // Actualizamos el prospecto con su respuesta
            $this->prospecto->update([
                'invitacion_confirmada' => $confirmado
            ]);

            if ($confirmado) {
                // Generar código único solo si asiste
                $codigo = 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

                // Defensa en profundidad: el <option> "bus" ya viene deshabilitado en el
                // formulario cuando no hay cupo, pero un tamper del campo oculto o una carrera
                // con otra confirmación no debe poder reservar un asiento de bus que ya no existe.
                if ($this->transporte === 'bus') {
                    $cupoBus = $this->evento->fresh()->cupoBusDisponible();

                    if ($cupoBus !== null && (1 + (int) $this->cantidad_acompanantes) > $cupoBus) {
                        $this->transporte = 'propio';
                    }
                }

                $invitado = InvitadoEntregaFest::create([
                    'entrega_fest_id' => $this->evento->id,
                    'prospecto_entrega_fest_id' => $this->prospecto->id,
                    'codigo_invitado' => $codigo,
                    'cantidad_acompanantes_permitidos' => $this->cantidad_acompanantes,
                    'confirmado' => true,
                    'transporte' => $this->transporte === 'bus' ? InvitadoEntregaFest::TRANSPORTE_BUS : InvitadoEntregaFest::TRANSPORTE_PROPIO,
                    'observaciones_asistencia' => $this->observaciones,
                ]);

                // Si tiene acompañantes, los registramos
                if ($this->cantidad_acompanantes > 0) {
                    foreach ($this->acompanantes as $acompanante) {
                        if (!empty($acompanante['dni']) && !empty($acompanante['nombres'])) {
                            \App\Models\AcompananteEntregaFest::create([
                                'dni' => $acompanante['dni'],
                                'nombres' => $acompanante['nombres'],
                                'email' => $acompanante['email'] ?? null,
                                'celular' => $acompanante['celular'] ?? null,
                                'prospecto_entrega_fest_id' => $this->prospecto->id,
                                'invitado_entrega_fest_id' => $invitado->id,
                            ]);
                        }
                    }
                }

                DB::commit();

                // Despachar evento para notificaciones (Email/WhatsApp)
                EntregaFestAsistenciaConfirmacion::dispatch($invitado);

                $this->codigo_invitado = $codigo;
            } else {
                DB::commit();
                $this->codigo_invitado = null;
            }

            $this->enviado = true;
            $this->mensaje_exito = $confirmado
                ? '¡Excelente! Tu asistencia ha sido confirmada. Nos vemos en el evento.'
                : 'Gracias por informarnos. Lamentamos que no puedas asistir esta vez.';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[ASISTENCIA PUBLICA] Error: " . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar tu solicitud. Por favor intenta más tarde.');
        }
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.asistencia-publica');
    }
}
