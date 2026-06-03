<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Area;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use App\Models\SubTipoSolicitud;
use App\Models\Ticket;
use App\Models\TicketDerivado;
use App\Models\TicketHistorial;
use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TicketHijosMasivos extends Component
{
    public Ticket $parentTicket;

    // Formulario para el hijo actual
    public $area_id = '';
    public $tipo_solicitud_id = '';
    public $sub_tipo_solicitud_id = '';
    public $canal_id = '';
    public $prioridad_ticket_id = '';
    public $gestor_id = '';
    public $asunto = '';
    public $descripcion = '';

    // Colección de hijos preparados
    public $hijosParaCrear = [];

    // Catálogos
    public $areas = [];
    public $tiposSolicitud = [];
    public $subTiposSolicitud = [];
    public $canales = [];
    public $prioridades = [];
    public $gestoresDisponibles = [];

    protected function obtenerTiposSolicitudPorArea($areaId)
    {
        $area = Area::find($areaId);

        if (!$area) {
            return collect();
        }

        return $area->tiposSolicitud()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
    }

    protected function obtenerGestoresPorArea($areaId, $tipoSolicitudId = null)
    {
        $area = Area::find($areaId);

        if (!$area) {
            return collect();
        }

        $idsDeArea = $area->users()->where('activo', true)->pluck('users.id')->toArray();

        if ($tipoSolicitudId && !empty($idsDeArea)) {
            $idsDeTipoSolicitud = DB::table('tipo_solicitud_user')
                ->where('tipo_solicitud_id', $tipoSolicitudId)
                ->whereIn('user_id', $idsDeArea)
                ->pluck('user_id')
                ->toArray();

            $idsFinales = !empty($idsDeTipoSolicitud) ? $idsDeTipoSolicitud : $idsDeArea;
        } else {
            $idsFinales = $idsDeArea;
        }

        return $area->users()
            ->whereIn('users.id', $idsFinales)
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();
    }

    protected function resolverGestorPorDefecto($gestores)
    {
        $principal = $gestores->first(fn($usuario) => (bool) $usuario->pivot->is_principal);

        return $principal?->id ?? $gestores->first()?->id;
    }

    protected function gestorPerteneceAArea($areaId, $gestorId, $tipoSolicitudId = null): bool
    {
        if (!$gestorId) {
            return false;
        }

        return $this->obtenerGestoresPorArea($areaId, $tipoSolicitudId)
            ->contains('id', (int) $gestorId);
    }

    public function mount(Ticket $ticket)
    {
        $this->parentTicket = $ticket;
        $this->areas = Area::where('activo', true)->orderBy('nombre')->get();
        $this->canales = Canal::orderBy('nombre')->get();
        $this->prioridades = PrioridadTicket::orderBy('id')->get();

        // Defaults desde el padre
        $this->area_id = (string)$this->parentTicket->area_id;
        $this->canal_id = (string)$this->parentTicket->canal_id;
        $this->prioridad_ticket_id = (string)$this->parentTicket->prioridad_ticket_id;
        $this->asunto = "HIJO: " . $this->parentTicket->asunto_inicial;

        $this->tiposSolicitud = $this->obtenerTiposSolicitudPorArea($this->area_id);

        $this->cargarGestores();
        $this->gestor_id = (string) ($this->resolverGestorPorDefecto($this->gestoresDisponibles) ?? '');
    }

    public function updatedAreaId($value)
    {
        $this->tiposSolicitud = $this->obtenerTiposSolicitudPorArea($value);
        $this->tipo_solicitud_id = '';
        $this->subTiposSolicitud = [];
        $this->sub_tipo_solicitud_id = '';
        $this->cargarGestores();
    }

    public function updatedTipoSolicitudId($value)
    {
        $this->subTiposSolicitud = $value
            ? SubTipoSolicitud::where('tipo_solicitud_id', $value)->where('activo', true)->orderBy('nombre')->get()
            : [];
        $this->sub_tipo_solicitud_id = '';
        $this->cargarGestores();
    }

    public function cargarGestores()
    {
        $this->gestoresDisponibles = collect();
        $this->gestor_id = '';

        if (!$this->area_id) return;

        $this->tiposSolicitud = $this->obtenerTiposSolicitudPorArea($this->area_id);
        $this->gestoresDisponibles = $this->obtenerGestoresPorArea($this->area_id, $this->tipo_solicitud_id);

        if (collect($this->gestoresDisponibles)->count() > 0) {
            $this->gestor_id = $this->resolverGestorPorDefecto($this->gestoresDisponibles) ?? '';
        }
    }

    public function agregarALista()
    {
        $this->validate([
            'area_id' => 'required',
            'tipo_solicitud_id' => 'required',
            'canal_id' => 'required',
            'prioridad_ticket_id' => 'required',
            'gestor_id' => 'required',
            'asunto' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $areaObj = Area::find($this->area_id);
        $gestorObj = User::find($this->gestor_id);

        $this->hijosParaCrear[] = [
            // Origen (datos capturados en el formulario que actuarán como origen de la derivación)
            'area_origen_id' => $this->area_id,
            'area_origen_nombre' => $areaObj->nombre,
            'gestor_origen_id' => $this->gestor_id,
            'gestor_origen_nombre' => $gestorObj->name,

            // Destino (inicialmente igual al origen, pero editable en la tabla)
            'area_id' => $this->area_id,
            'gestor_id' => $this->gestor_id,

            'tipo_solicitud_id' => $this->tipo_solicitud_id,
            'tipo_solicitud_nombre' => TipoSolicitud::find($this->tipo_solicitud_id)->nombre,
            'sub_tipo_solicitud_id' => $this->sub_tipo_solicitud_id,
            'canal_id' => $this->canal_id,
            'prioridad_ticket_id' => $this->prioridad_ticket_id,
            'asunto' => $this->asunto,
            'descripcion' => $this->descripcion,
        ];

        $this->reset(['asunto', 'descripcion', 'sub_tipo_solicitud_id', 'tipo_solicitud_id']);

        // Restaurar los defaults del padre para agilizar la carga del siguiente
        $this->area_id = (string)$this->parentTicket->area_id;
        $this->prioridad_ticket_id = (string)$this->parentTicket->prioridad_ticket_id;
        $this->canal_id = (string)$this->parentTicket->canal_id;
        $this->asunto = "HIJO: " . $this->parentTicket->asunto_inicial;

        $this->cargarGestores();
        $this->gestor_id = (string) ($this->resolverGestorPorDefecto($this->gestoresDisponibles) ?? '');
    }

    public function updated($name, $value)
    {
        // Si cambia el área de una fila en la tabla, reseteamos el gestor de esa fila
        if (str_starts_with($name, 'hijosParaCrear.') && str_ends_with($name, '.area_id')) {
            $parts = explode('.', $name);
            $index = $parts[1];
            $this->hijosParaCrear[$index]['gestor_id'] = '';
        }
    }

    public function getGestoresPorArea($areaId, $tipoSolicitudId = null)
    {
        if (!$areaId) return collect();

        $area = Area::find($areaId);
        if (!$area) return collect();

        // 1. Obtener IDs de usuarios asignados al área
        $idsDeArea = $area->users()
            ->where('activo', true)
            ->pluck('users.id')
            ->toArray();

        // 2. Si hay tipo_solicitud, hacer intersección con tipo_solicitud_user
        if ($tipoSolicitudId && !empty($idsDeArea)) {
            $idsDeTipoSolicitud = DB::table('tipo_solicitud_user')
                ->where('tipo_solicitud_id', $tipoSolicitudId)
                ->whereIn('user_id', $idsDeArea)
                ->pluck('user_id')
                ->toArray();

            // Si hay coincidencia, usar solo esos; si no hay ninguno, caer de nuevo a todos los del área
            $idsFinales = !empty($idsDeTipoSolicitud) ? $idsDeTipoSolicitud : $idsDeArea;
        } else {
            $idsFinales = $idsDeArea;
        }

        // 3. Cargar gestores con el pivot de area para saber el principal del área
        return $area->users()
            ->whereIn('users.id', $idsFinales)
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();
    }

    public function quitarDeLista($index)
    {
        unset($this->hijosParaCrear[$index]);
        $this->hijosParaCrear = array_values($this->hijosParaCrear);
    }

    public function crearHijosMasivos()
    {
        if (empty($this->hijosParaCrear)) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Lista vacía', 'text' => 'Debes agregar al menos un ticket hijo.']);
            return;
        }

        foreach ($this->hijosParaCrear as $index => $h) {
            if (!$this->gestorPerteneceAArea($h['area_id'], $h['gestor_id'], $h['tipo_solicitud_id'])) {
                $this->addError("hijosParaCrear.$index.gestor_id", 'El gestor debe pertenecer al área seleccionada.');
                $this->dispatch('alertaLivewire', [
                    'type' => 'warning',
                    'title' => 'Advertencia',
                    'text' => 'Hay un ticket hijo con un gestor que no pertenece al área seleccionada.'
                ]);
                return;
            }
        }

        try {
            DB::beginTransaction();

            $estadoDerivadoId = EstadoTicket::id(EstadoTicket::DERIVADO);

            foreach ($this->hijosParaCrear as $h) {
                $gestorDestinoId = $h['gestor_id'];
                if (!$this->gestorPerteneceAArea($h['area_id'], $gestorDestinoId, $h['tipo_solicitud_id'])) {
                    $gestorDestinoId = $this->resolverGestorPorDefecto($this->obtenerGestoresPorArea($h['area_id'], $h['tipo_solicitud_id']));
                }

                $ticketHijo = Ticket::create([
                    'id_empresa' => $this->parentTicket->id_empresa,
                    'unidad_negocio_id' => $this->parentTicket->unidad_negocio_id,
                    'proyecto_id' => $this->parentTicket->proyecto_id,
                    'cliente_id' => $this->parentTicket->cliente_id,
                    'dni' => $this->parentTicket->dni,
                    'nombres' => $this->parentTicket->nombres,
                    'email' => $this->parentTicket->email,
                    'celular' => $this->parentTicket->celular,
                    'direccion' => $this->parentTicket->direccion,
                    'atencion_id' => $this->parentTicket->atencion_id,

                    'ticket_padre_id' => $this->parentTicket->id,

                    'area_id' => $h['area_id'],
                    'tipo_solicitud_id' => $h['tipo_solicitud_id'],
                    'sub_tipo_solicitud_id' => $h['sub_tipo_solicitud_id'],
                    'canal_id' => $h['canal_id'],
                    'prioridad_ticket_id' => $h['prioridad_ticket_id'],
                    'gestor_id' => $gestorDestinoId,
                    'asunto_inicial' => $h['asunto'],
                    'descripcion_inicial' => $h['descripcion'],
                    'estado_ticket_id' => $estadoDerivadoId,
                    'created_by' => Auth::id(),
                    'lotes' => $this->parentTicket->lotes,
                    'origen' => $this->parentTicket->origen,
                ]);

                // Registrar la derivación (desde el área de origen seleccionada al área de destino)
                TicketDerivado::create([
                    'ticket_id' => $ticketHijo->id,
                    'de_area_id' => $h['area_origen_id'],
                    'a_area_id' => $ticketHijo->area_id,
                    'usuario_deriva_id' => Auth::id(),
                    'usuario_recibe_id' => $ticketHijo->gestor_id,
                    'motivo' => "Derivación automática en creación masiva de hijo.",
                ]);

                // Registrar participantes
                $ticketHijo->usuariosParticipantes()->syncWithoutDetaching([
                    Auth::id(),
                    (int) $ticketHijo->gestor_id
                ]);

                TicketHistorial::create([
                    'ticket_id' => $ticketHijo->id,
                    'user_id' => Auth::id(),
                    'accion' => 'Creación y Derivación',
                    'detalle' => "Ticket creado como hijo masivo. Derivado de {$h['area_origen_nombre']} por cuenta del gestor {$h['gestor_origen_nombre']}.",
                ]);
            }

            TicketHistorial::create([
                'ticket_id' => $this->parentTicket->id,
                'user_id' => Auth::id(),
                'accion' => 'Hijos Masivos',
                'detalle' => "Se crearon " . count($this->hijosParaCrear) . " tickets hijos asociados.",
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['type' => 'success', 'title' => 'Éxito', 'text' => 'Se crearon los tickets hijos correctamente.']);
            $this->dispatch('cerrarModalHijosMasivos');
            $this->dispatch('refreshHijos'); // Para que el padre actualice su lista si es necesario

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[TICKET HIJOS MASIVOS] " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'Ocurrió un error al crear los tickets.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-hijos-masivos');
    }
}
