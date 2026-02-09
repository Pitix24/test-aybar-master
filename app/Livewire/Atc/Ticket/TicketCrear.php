<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Ticket;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Area;
use App\Models\TipoSolicitud;
use App\Models\SubTipoSolicitud;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class TicketCrear extends Component
{
    // Datos del Ticket
    public $unidad_negocio_id = '';
    public $proyecto_id = '';
    public $cliente_id = '';
    public $area_id = '';
    public $tipo_solicitud_id = '';
    public $sub_tipo_solicitud_id = '';
    public $canal_id = '';
    public $estado_ticket_id = 1; // Por defecto el primero
    public $prioridad_ticket_id = 3; // Por defecto Media
    public $gestor_id = '';
    public $asunto_inicial = '';
    public $descripcion_inicial = '';
    public $ticket_padre_id = null;

    // Datos Participantes
    public $searchUser = '';
    public $selectedParticipants = []; // IDs de usuarios

    protected function rules()
    {
        return [
            'asunto_inicial' => 'required|min:5|max:255',
            'descripcion_inicial' => 'required|min:10',
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'cliente_id' => 'required|exists:users,id',
            'area_id' => 'required|exists:areas,id',
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',
            'sub_tipo_solicitud_id' => 'nullable|exists:sub_tipo_solicituds,id',
            'canal_id' => 'required|exists:canals,id',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
            'prioridad_ticket_id' => 'required|exists:prioridad_tickets,id',
            'gestor_id' => 'nullable|exists:users,id',
            'ticket_padre_id' => 'nullable|exists:tickets,id',
            'selectedParticipants' => 'nullable|array',
            'selectedParticipants.*' => 'exists:users,id',
        ];
    }

    public function mount($ticketPadre = null)
    {
        if ($ticketPadre) {
            $padre = Ticket::findOrFail($ticketPadre);
            $this->ticket_padre_id = $padre->id;
            $this->unidad_negocio_id = $padre->unidad_negocio_id;
            $this->proyecto_id = $padre->proyecto_id;
            $this->cliente_id = $padre->cliente_id;
            $this->asunto_inicial = "RE: " . $padre->asunto_inicial;
        }
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
    }

    public function updatedTipoSolicitudId($value)
    {
        $this->sub_tipo_solicitud_id = '';
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
                'cliente_id' => $this->cliente_id,
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
                'created_by' => auth()->id(),
            ]);

            // Guardar participantes
            if (!empty($this->selectedParticipants)) {
                $ticket->usuariosParticipantes()->sync($this->selectedParticipants);
            }

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
        $clientes = User::where('rol', 'cliente')->where('activo', true)->orderBy('name')->get();
        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $tipos = TipoSolicitud::where('activo', true)->orderBy('nombre')->get();
        $subtipos = SubTipoSolicitud::where('tipo_solicitud_id', $this->tipo_solicitud_id)->where('activo', true)->orderBy('nombre')->get();
        $canales = Canal::where('activo', true)->orderBy('nombre')->get();
        $estados = EstadoTicket::where('activo', true)->get();
        $prioridades = PrioridadTicket::where('activo', true)->get();
        $gestores = User::where('rol', 'admin')->where('activo', true)->orderBy('name')->get();

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
            'clientes',
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
