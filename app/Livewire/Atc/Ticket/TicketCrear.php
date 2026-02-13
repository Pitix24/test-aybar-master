<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Cliente;
use App\Models\Ticket;
use App\Models\TicketHistorial;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Area;
use App\Models\SubTipoSolicitud;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use App\Services\ConsultaClienteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

#[Title('Crear Ticket')]
#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class TicketCrear extends Component
{
    public Ticket $ticketPadre;
    public ?int $ticket_padre_id = null;
    public ?Area $area_seleccionada = null;

    public $areas = [], $area_id = "";
    public $tipos_solicitudes = [], $tipo_solicitud_id = "";
    public $sub_tipos_solicitudes = [], $sub_tipo_solicitud_id = "";
    public $canales = [], $canal_id = "";
    public $cliente, $cliente_id = "", $origen = "";

    public $gestores = [], $gestor_id = "";
    public $asunto_inicial = '';
    public $descripcion_inicial = '';
    public $dni = '';
    public $nombres = '';
    public $email = '';
    public $celular = '';
    public $lote_id = '';
    public $lotes_agregados = [];

    public $unidades_negocios = [], $unidad_negocio_id = '';
    public $proyectos = [], $proyecto_id = '';

    public $prioridades = [], $prioridad_ticket_id = '';

    public $searchUser = '';
    public $selectedParticipants = [];

    public Collection $informaciones;

    protected function rules()
    {
        $rules = [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'area_id' => 'required|exists:areas,id',
            'tipo_solicitud_id' => 'required',
            'sub_tipo_solicitud_id' => 'nullable',
            'canal_id' => 'required|exists:canals,id',
            'gestor_id' => 'nullable',
            'asunto_inicial' => 'required|min:5|max:255',
            'descripcion_inicial' => 'required|min:10',
            'prioridad_ticket_id' => 'required|exists:prioridad_tickets,id',
            'ticket_padre_id' => 'nullable|exists:tickets,id',
            'selectedParticipants' => 'nullable|array',
            'selectedParticipants.*' => 'exists:users,id',
        ];

        if (!$this->ticket_padre_id) {
            $rules['dni'] = 'required';
            $rules['nombres'] = 'required';
        }

        return $rules;
    }

    public function mount($ticketPadre = null)
    {
        if ($ticketPadre) {
            // Si ya es una instancia de Ticket (Route Model Binding)
            if ($ticketPadre instanceof Ticket) {
                $this->ticketPadre = $ticketPadre;
            }
            // Si es un array (a veces Livewire pasa los parámetros así)
            elseif (is_array($ticketPadre)) {
                $id = $ticketPadre['id'] ?? reset($ticketPadre);
                $this->ticketPadre = Ticket::findOrFail($id);
            }
            // Si es un ID (string o int)
            else {
                $this->ticketPadre = Ticket::findOrFail($ticketPadre);
            }

            $this->ticket_padre_id = $this->ticketPadre->id;
            $this->unidad_negocio_id = $this->ticketPadre->unidad_negocio_id;
            $this->proyecto_id = $this->ticketPadre->proyecto_id;
            $this->canal_id = $this->ticketPadre->canal_id;
            $this->dni = $this->ticketPadre->dni;
            $this->nombres = $this->ticketPadre->nombres;
            $this->email = $this->ticketPadre->email;
            $this->celular = $this->ticketPadre->celular;
            $this->origen = $this->ticketPadre->origen;
            $this->loadProyectos();
        }

        $user = auth()->user();

        // Determinar área inicial
        if ($user->areas()->exists()) {
            $this->area_id = $user->areas()->orderBy('area_user.created_at')->value('areas.id');
        } else {
            $this->area_id = Area::where('activo', true)->orderBy('id')->value('id');
        }

        // Cargar catálogos activos
        $this->areas = Area::where('activo', true)->orderBy('nombre')->get();
        $this->canales = Canal::where('activo', true)->orderBy('nombre')->get();
        $this->unidades_negocios = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $this->prioridades = PrioridadTicket::where('activo', true)->get();

        // Valores por defecto
        if (!$this->ticket_padre_id) {
            $this->prioridad_ticket_id = $this->prioridades->firstWhere('nombre', 'Media')?->id ?? $this->prioridades->first()?->id;
        }

        // Añadir al creador como participante por defecto
        if (!in_array($user->id, $this->selectedParticipants)) {
            $this->selectedParticipants[] = $user->id;
        }

        $this->informaciones = collect();

        if ($this->area_id) {
            $this->cargarDatosArea($this->area_id);
        }
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        if ($value) {
            $this->loadProyectos();
        }
    }

    public function loadProyectos()
    {
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        }
    }

    public function cargarDatosArea($areaId)
    {
        $area = Area::find($areaId);

        if (!$area) {
            $this->area_seleccionada = null;
            $this->tipos_solicitudes = collect();
            $this->gestores = collect();
            $this->gestor_id = null;
            $this->tipo_solicitud_id = null;
            return;
        }

        $this->area_seleccionada = $area;

        $this->tipos_solicitudes = $area->tiposSolicitud()
            ->where('activo', true)
            ->get();

        $this->gestores = $area->users()
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();

        $user = Auth::user();

        if ($this->gestores->contains('id', $user->id)) {
            $this->gestor_id = $user->id;
        } else {
            $principal = $this->gestores
                ->first(fn($u) => (bool) $u->pivot->is_principal);

            if ($principal) {
                $this->gestor_id = $principal->id;
            } else {
                $this->gestor_id = $this->gestores->first()?->id;
            }
        }

        $this->tipo_solicitud_id = '';
        $this->sub_tipo_solicitud_id = '';
    }

    public function updatedAreaId($value)
    {
        $this->cargarDatosArea($value);
    }

    public function updatedTipoSolicitudId($value)
    {
        $this->sub_tipo_solicitud_id = '';

        if ($value) {
            $this->loadSubTipoSolicitudes();
        }
    }

    public function loadSubTipoSolicitudes()
    {
        if ($this->tipo_solicitud_id) {
            $this->sub_tipos_solicitudes = SubTipoSolicitud::where('tipo_solicitud_id', $this->tipo_solicitud_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        }
    }

    public function buscarCliente(ConsultaClienteService $service)
    {
        $this->validate(['dni' => 'required']);

        $resultado = $service->consultar($this->dni);

        switch ($resultado['estado']) {
            case 'ok':
                session()->flash('success', $resultado['mensaje']);
                $this->informaciones = collect($resultado['data']);

                if ($resultado['origen'] === 'antiguo') {
                    $this->cliente = DB::table('clientes_2')->where('dni', $this->dni)->first();
                    $this->cliente_id = null;
                    $this->nombres = $this->cliente->nombre;
                    $this->email = $this->cliente->email ?? '';
                    $this->celular = $this->cliente->celular ?? $this->cliente->telefono ?? '';
                    $this->origen = "antiguo";
                } elseif ($resultado['origen'] === 'slin') {
                    $this->cliente = Cliente::where('dni', $this->dni)->first();
                    if ($this->cliente) {
                        $this->cliente_id = $this->cliente->user_id ?? $this->cliente->user->id;
                        $this->nombres = $this->cliente->user->name;
                        $this->email = $this->cliente->user->email;
                        $this->celular = $this->cliente->celular ?? '';
                    } else {
                        session()->flash('info', 'Debes crear la cuenta del cliente.');
                        $firstLot = collect($this->informaciones)->first();
                        $this->nombres = $firstLot->nombre ?? $firstLot->razon_social ?? '';
                        $this->email = $firstLot->email ?? '';
                        $this->celular = $firstLot->celular ?? '';
                    }
                    $this->origen = "slin";
                }
                break;

            case 'cliente_sin_lotes':
                session()->flash('info', $resultado['mensaje']);
                $this->informaciones = collect();
                $this->cliente = Cliente::where('dni', $this->dni)->first();
                if ($this->cliente) {
                    $this->cliente_id = $this->cliente->user_id ?? $this->cliente->user->id;
                    $this->nombres = $this->cliente->user->name;
                    $this->email = $this->cliente->user->email;
                    $this->celular = $this->cliente->celular ?? '';
                } else {
                    $this->nombres = "SIN CUENTA VINCULADA";
                    $this->email = '';
                    $this->celular = '';
                }
                $this->origen = "slin";
                break;

            case 'no_cliente':
            case 'error':
                session()->flash('error', $resultado['mensaje']);
                $this->informaciones = collect();
                $this->nombres = '';
                break;
        }
    }

    public function agregarLote()
    {
        if (!$this->lote_id)
            return;

        $lote = $this->informaciones->firstWhere('id', $this->lote_id);
        if (!$lote)
            return;

        if (collect($this->lotes_agregados)->firstWhere('id', $lote->id))
            return;

        $this->lotes_agregados[] = [
            'id' => $lote->id,
            'razon_social' => $lote->razon_social,
            'proyecto' => $lote->proyecto,
            'numero_lote' => $lote->numero_lote,
        ];

        $this->lote_id = "";
    }

    public function quitarLote($id)
    {
        $this->lotes_agregados = collect($this->lotes_agregados)
            ->reject(fn($l) => $l['id'] == $id)
            ->values()
            ->toArray();
    }

    public function addParticipant($userId)
    {
        if (!in_array($userId, $this->selectedParticipants)) {
            $this->selectedParticipants[] = $userId;
        }
        $this->searchUser = '';
    }

    public function removeParticipant($userId)
    {
        $this->selectedParticipants = array_diff($this->selectedParticipants, [$userId]);
    }

    public function store($confirmado = false, $modoLote = null)
    {
        abort_unless(auth()->user()->can('ticket.crear'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Faltan campos obligatorios.']);
            throw $e;
        }

        // 1. Validación de contacto (Prioridad 1)
        if (!$confirmado && (empty($this->email) || empty($this->celular))) {
            $this->dispatch('confirmarTicketSinDatos');
            return;
        }

        // 2. Preguntar por lotes si hay más de uno y no se ha decidido el modo (Prioridad 2)
        if (count($this->lotes_agregados) > 1 && is_null($modoLote)) {
            $this->dispatch('preguntarModoLotes');
            return;
        }

        try {
            DB::beginTransaction();

            $estadoAbiertoId = EstadoTicket::id(EstadoTicket::NUEVO);

            // Definimos cómo iterar según el modo elegido o la cantidad de lotes
            // Si el modo es 'separados', creamos uno por cada lote.
            // Si el modo es 'juntos' o solo hay 0-1 lote, creamos solo uno.
            $lotesIterar = ($modoLote === 'separados' && count($this->lotes_agregados) > 1)
                ? $this->lotes_agregados
                : [null];

            foreach ($lotesIterar as $loteIndividual) {
                // Si estamos en modo separados, asignamos solo ese lote.
                // Si estamos en modo juntos (o solo hay uno), asignamos todo el array.
                $lotesParaTicket = $loteIndividual ? [$loteIndividual] : $this->lotes_agregados;

                $ticket = Ticket::create([
                    'unidad_negocio_id' => $this->unidad_negocio_id,
                    'proyecto_id' => $this->proyecto_id,
                    'cliente_id' => $this->cliente_id ?: null,
                    'area_id' => $this->area_id,
                    'ticket_padre_id' => $this->ticket_padre_id,
                    'tipo_solicitud_id' => $this->tipo_solicitud_id,
                    'sub_tipo_solicitud_id' => $this->sub_tipo_solicitud_id ?: null,
                    'canal_id' => $this->canal_id,
                    'estado_ticket_id' => $estadoAbiertoId,
                    'prioridad_ticket_id' => $this->prioridad_ticket_id,
                    'gestor_id' => $this->gestor_id ?: null,
                    'asunto_inicial' => $this->asunto_inicial,
                    'descripcion_inicial' => $this->descripcion_inicial,
                    'dni' => $this->dni,
                    'nombres' => $this->nombres,
                    'email' => $this->email,
                    'celular' => $this->celular,
                    'origen' => $this->origen,
                    'lotes' => $lotesParaTicket,
                    'created_by' => auth()->id(),
                ]);

                if (!empty($this->selectedParticipants)) {
                    $ticket->usuariosParticipantes()->sync($this->selectedParticipants);
                }

                // Historial
                TicketHistorial::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                    'accion' => 'Creación',
                    'detalle' => 'Ticket creado con estado: ' . $ticket->estado?->nombre,
                ]);
            }

            DB::commit();

            $mensaje = ($modoLote === 'separados' && count($this->lotes_agregados) > 1)
                ? 'Se han generado ' . count($this->lotes_agregados) . ' tickets (separados por lote).'
                : (count($this->lotes_agregados) > 1
                    ? 'Se ha generado 1 ticket con todos los lotes agrupados.'
                    : 'El ticket ha sido generado correctamente.');

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => $mensaje]);
            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al guardar el ticket.']);
            return;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        $participantesDisponibles = [];
        if (strlen($this->searchUser) > 2) {
            $participantesDisponibles = User::where('activo', true)
                ->where('name', 'like', "%{$this->searchUser}%")
                ->whereNotIn('id', $this->selectedParticipants)
                ->limit(5)
                ->get();
        }

        $participantesSeleccionados = User::whereIn('id', $this->selectedParticipants)->get();

        return view('livewire.atc.ticket.ticket-crear', [
            'participantesDisponibles' => $participantesDisponibles,
            'participantesSeleccionados' => $participantesSeleccionados
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
