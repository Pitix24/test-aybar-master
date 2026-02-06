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
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class TicketEditar extends Component
{
    public Ticket $ticket;

    // Datos del Ticket
    public $unidad_negocio_id;
    public $proyecto_id;
    public $cliente_id;
    public $area_id;
    public $tipo_solicitud_id;
    public $sub_tipo_solicitud_id;
    public $canal_id;
    public $estado_ticket_id;
    public $prioridad_ticket_id;
    public $gestor_id;
    public $asunto_inicial;
    public $descripcion_inicial;

    // Datos Participantes
    public $searchUser = '';
    public $selectedParticipants = [];

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
            'selectedParticipants' => 'nullable|array',
            'selectedParticipants.*' => 'exists:users,id',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::findOrFail($id);

        $this->unidad_negocio_id = $this->ticket->unidad_negocio_id;
        $this->proyecto_id = $this->ticket->proyecto_id;
        $this->cliente_id = $this->ticket->cliente_id;
        $this->area_id = $this->ticket->area_id;
        $this->tipo_solicitud_id = $this->ticket->tipo_solicitud_id;
        $this->sub_tipo_solicitud_id = $this->ticket->sub_tipo_solicitud_id;
        $this->canal_id = $this->ticket->canal_id;
        $this->estado_ticket_id = $this->ticket->estado_ticket_id;
        $this->prioridad_ticket_id = $this->ticket->prioridad_ticket_id;
        $this->gestor_id = $this->ticket->gestor_id;
        $this->asunto_inicial = $this->ticket->asunto_inicial;
        $this->descripcion_inicial = $this->ticket->descripcion_inicial;

        $this->selectedParticipants = $this->ticket->usuariosParticipantes()->pluck('users.id')->toArray();
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

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Faltan campos obligatorios.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->ticket->update([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'cliente_id' => $this->cliente_id,
                'area_id' => $this->area_id,
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'sub_tipo_solicitud_id' => $this->sub_tipo_solicitud_id ?: null,
                'canal_id' => $this->canal_id,
                'estado_ticket_id' => $this->estado_ticket_id,
                'prioridad_ticket_id' => $this->prioridad_ticket_id,
                'gestor_id' => $this->gestor_id ?: null,
                'asunto_inicial' => $this->asunto_inicial,
                'descripcion_inicial' => $this->descripcion_inicial,
                'updated_by' => auth()->id(),
            ]);

            // Sincronizar participantes
            $this->ticket->usuariosParticipantes()->sync($this->selectedParticipants);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'El ticket ha sido actualizado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al actualizar el ticket.']);
            return;
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        try {
            DB::beginTransaction();
            $this->ticket->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El ticket ha sido eliminado.']);
            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar el ticket.']);
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

        return view('livewire.atc.ticket.ticket-editar', compact(
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
        <x-erp.placeholder />
        HTML;
    }
}
