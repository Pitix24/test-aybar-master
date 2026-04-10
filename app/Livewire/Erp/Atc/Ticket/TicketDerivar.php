<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Area;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use App\Models\TicketDerivado;
use App\Models\TicketHistorial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Title('Derivar Ticket')]
#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class TicketDerivar extends Component
{
    public Ticket $ticket;

    public $a_area_id = '';
    public $gestor_id = '';
    public $motivo = '';

    public $areas = [];
    public $gestores = [];

    public $mapAreas = [];
    public $mapUsuarios = [];

    protected function rules()
    {
        return [
            'a_area_id' => 'required|exists:areas,id',
            'gestor_id' => 'required|exists:users,id',
            'motivo' => 'required|string|min:10|max:2000',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::with(['area', 'gestor', 'estado'])->findOrFail($id);

        $this->areas = Area::where('activo', true)
            //->where('id', '!=', $this->ticket->area_id)
            ->orderBy('nombre')
            ->get();

        $this->mapAreas = Area::pluck('nombre', 'id')->toArray();
        $this->mapUsuarios = User::pluck('name', 'id')->toArray();
    }

    public function updatedAAreaId($value)
    {
        $this->gestores = collect();
        $this->gestor_id = null;

        if (!$value) {
            return;
        }

        $area = Area::find($value);
        if (!$area) {
            return;
        }

        // 1. Obtener IDs de usuarios asignados al área
        $idsDeArea = $area->users()
            ->where('activo', true)
            ->pluck('users.id')
            ->toArray();

        // 2. Si el ticket tiene tipo_solicitud, hacer intersección con tipo_solicitud_user
        $tipoSolicitudId = $this->ticket->tipo_solicitud_id;

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
        $this->gestores = $area->users()
            ->whereIn('users.id', $idsFinales)
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();

        if ($this->gestores->isEmpty()) {
            return;
        }

        // 4. Preseleccionar: primero buscar principal de tipo_solicitud_user, luego principal de área
        if ($tipoSolicitudId) {
            $principalTipo = DB::table('tipo_solicitud_user')
                ->where('tipo_solicitud_id', $tipoSolicitudId)
                ->where('is_principal', true)
                ->whereIn('user_id', $idsFinales)
                ->value('user_id');

            if ($principalTipo) {
                $this->gestor_id = $principalTipo;
                return;
            }
        }

        // Fallback: principal del área
        $principal = $this->gestores->first(fn($u) => (bool) $u->pivot->is_principal);
        $this->gestor_id = $principal
            ? $principal->id
            : $this->gestores->first()->id;
    }

    public function store()
    {
        $this->authorize('ticket.derivar');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $oldArea = $this->mapAreas[$this->ticket->area_id] ?? 'N/A';
            $oldGestor = $this->ticket->gestor->name ?? 'Sin asignar';

            $newAreaName = $this->mapAreas[$this->a_area_id] ?? 'N/A';
            $newGestorName = $this->mapUsuarios[$this->gestor_id] ?? 'N/A';

            TicketDerivado::create([
                'ticket_id' => $this->ticket->id,
                'de_area_id' => $this->ticket->area_id,
                'a_area_id' => $this->a_area_id,
                'usuario_deriva_id' => auth()->id(),
                'usuario_recibe_id' => $this->gestor_id,
                'motivo' => $this->motivo,
            ]);

            $estadoDerivadoId = EstadoTicket::id(EstadoTicket::DERIVADO);

            $this->ticket->update([
                'area_id' => $this->a_area_id,
                'gestor_id' => $this->gestor_id,
                'estado_ticket_id' => $estadoDerivadoId,
                'updated_by' => auth()->id(),
            ]);

            // Registrar participantes: el que deriva y el que recibe
            $this->ticket->usuariosParticipantes()->syncWithoutDetaching([
                auth()->id(),
                (int) $this->gestor_id
            ]);

            $detalle = "Ticket derivado de Área '$oldArea' a '$newAreaName' | ";
            $detalle .= "Gestor cambiado de '$oldGestor' a '$newGestorName' | ";
            $detalle .= "Motivo: {$this->motivo}";

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Derivación',
                'detalle' => $detalle,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'title' => '¡Derivado!',
                'text' => 'El ticket ha sido derivado correctamente.'
            ]);

            return redirect()->route('erp.ticket.vista.editar', $this->ticket->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error TicketDerivar@derivar: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No se pudo procesar la derivación.'
            ]);
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-derivar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
