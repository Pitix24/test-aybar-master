<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Cliente;
use App\Models\Ticket;
use App\Models\TicketHistorial;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Area;
use App\Models\TipoSolicitud;
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

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class TicketCrear extends Component
{
    // Ticket Padre
    public $ticketPadre;
    public ?int $ticket_padre_id = null;

    // Datos del Ticket
    public $unidad_negocio_id = '';
    public $proyecto_id = '';
    public $cliente_id = ''; // ID de usuario si existe
    public $area_id = '';
    public $tipo_solicitud_id = '';
    public $sub_tipo_solicitud_id = '';
    public $canal_id = '';
    public $estado_ticket_id = 1; // Por defecto el primero
    public $prioridad_ticket_id = 3; // Por defecto Media
    public $gestor_id = '';
    public $asunto_inicial = '';
    public $descripcion_inicial = '';

    // Cliente lookup y datos adicionales
    public $dni = '';
    public $nombres = '';
    public $origen = ''; // slin, antiguo
    public $cliente; // Objeto cliente/usuario
    public $informaciones;

    // Lotes
    public $lote_id = '';
    public $lotes_agregados = [];

    // Datos Participantes
    public $searchUser = '';
    public $selectedParticipants = []; // IDs de usuarios

    protected function rules()
    {
        $rules = [
            'asunto_inicial' => 'required|min:5|max:255',
            'descripcion_inicial' => 'required|min:10',
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'area_id' => 'required|exists:areas,id',
            'tipo_solicitud_id' => 'required',
            'sub_tipo_solicitud_id' => 'nullable',
            'canal_id' => 'required|exists:canals,id',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
            'prioridad_ticket_id' => 'required|exists:prioridad_tickets,id',
            'gestor_id' => 'nullable',
            'ticket_padre_id' => 'nullable|exists:tickets,id',
            'selectedParticipants' => 'nullable|array',
            'selectedParticipants.*' => 'exists:users,id',
        ];

        if (!$this->ticket_padre_id) {
            $rules['dni'] = 'required';
        }

        return $rules;
    }

    public function mount($ticketPadre = null)
    {
        $this->informaciones = collect();

        if ($ticketPadre) {
            $this->ticketPadre = Ticket::findOrFail($ticketPadre);
            $this->ticket_padre_id = $this->ticketPadre->id;
            $this->unidad_negocio_id = $this->ticketPadre->unidad_negocio_id;
            $this->proyecto_id = $this->ticketPadre->proyecto_id;
            $this->cliente_id = $this->ticketPadre->cliente_id;
            $this->dni = $this->ticketPadre->dni;
            $this->nombres = $this->ticketPadre->nombres;
            $this->origen = $this->ticketPadre->origen;
            $this->canal_id = $this->ticketPadre->canal_id;
            $this->asunto_inicial = "RE: " . $this->ticketPadre->asunto_inicial;
        }

        // Seleccionar área por defecto del usuario
        $user = auth()->user();
        if ($user->areas()->exists()) {
            $this->area_id = $user->areas()->orderBy('area_user.created_at')->value('areas.id');
        } else {
            $this->area_id = Area::orderBy('id')->value('id');
        }

        if ($this->area_id) {
            $this->cargarDatosArea($this->area_id);
        }
    }

    public function updatedAreaId($value)
    {
        $this->cargarDatosArea($value);
    }

    public function cargarDatosArea($areaId)
    {
        $area = Area::find($areaId);
        if (!$area)
            return;

        $gestoresDisp = $area->users()
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();

        $user = auth()->user();
        if ($gestoresDisp->contains('id', $user->id)) {
            $this->gestor_id = $user->id;
        } else {
            $principal = $gestoresDisp->first(fn($u) => (bool) $u->pivot->is_principal);
            $this->gestor_id = $principal ? $principal->id : $gestoresDisp->first()?->id;
        }

        $this->tipo_solicitud_id = '';
        $this->sub_tipo_solicitud_id = '';
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
    }

    public function updatedTipoSolicitudId($value)
    {
        $this->sub_tipo_solicitud_id = '';
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
                    $this->cliente_id = null; // No tiene ID en users aún
                    $this->nombres = $this->cliente->nombre;
                    $this->origen = "antiguo";
                } elseif ($resultado['origen'] === 'slin') {
                    $this->cliente = Cliente::where('dni', $this->dni)->first();
                    if ($this->cliente) {
                        $this->cliente_id = $this->cliente->user_id ?? $this->cliente->user->id;
                        $this->nombres = $this->cliente->user->name;
                    } else {
                        session()->flash('info', 'Debes crear la cuenta del cliente.');
                        $firstLot = collect($this->informaciones)->first();
                        $this->nombres = $firstLot->nombre ?? $firstLot->razon_social ?? '';
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
                } else {
                    $this->nombres = "SIN CUENTA VINCULADA";
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

    public function store()
    {
        abort_unless(auth()->user()->can('ticket.crear'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Faltan campos obligatorios.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $ticket = Ticket::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'cliente_id' => $this->cliente_id ?: null,
                'area_id' => $this->area_id,
                'ticket_padre_id' => $this->ticket_padre_id,
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'sub_tipo_solicitud_id' => $this->sub_tipo_solicitud_id ?: null,
                'canal_id' => $this->canal_id,
                'estado_ticket_id' => $this->estado_ticket_id,
                'prioridad_ticket_id' => $this->prioridad_ticket_id,
                'gestor_id' => $this->gestor_id ?: null,
                'asunto_inicial' => $this->asunto_inicial,
                'descripcion_inicial' => $this->descripcion_inicial,
                'dni' => $this->dni,
                'nombres' => $this->nombres,
                'origen' => $this->origen,
                'lotes' => $this->lotes_agregados,
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

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'El ticket ha sido generado correctamente.']);
            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al guardar el ticket.']);
            return;
        }
    }

    public function render()
    {
        $unidades = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->where('activo', true)->orderBy('nombre')->get();
        $areas = Area::where('activo', true)->orderBy('nombre')->get();

        $areaSel = Area::find($this->area_id);
        $tipos = $areaSel ? $areaSel->tiposSolicitud()->where('activo', true)->get() : collect();
        $subtipos = SubTipoSolicitud::where('tipo_solicitud_id', $this->tipo_solicitud_id)->where('activo', true)->orderBy('nombre')->get();

        $canales = Canal::where('activo', true)->orderBy('nombre')->get();
        $estados = EstadoTicket::where('activo', true)->get();
        $prioridades = PrioridadTicket::where('activo', true)->get();

        $gestores = $areaSel ? $areaSel->users()->where('activo', true)->orderBy('users.name')->get() : collect();

        $participantesDisponibles = [];
        if (strlen($this->searchUser) > 2) {
            $participantesDisponibles = User::where('activo', true)
                ->where('name', 'like', "%{$this->searchUser}%")
                ->whereNotIn('id', $this->selectedParticipants)
                ->limit(5)
                ->get();
        }

        $participantesSeleccionados = User::whereIn('id', $this->selectedParticipants)->get();

        return view('livewire.atc.ticket.ticket-crear', compact(
            'unidades',
            'proyectos',
            'areas',
            'tipos',
            'subtipos',
            'canales',
            'estados',
            'prioridades',
            'gestores',
            'participantesDisponibles',
            'participantesSeleccionados'
        ));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
