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
use Illuminate\Support\Facades\Auth;
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

    protected function obtenerGestoresPorArea($areaId, $tipoSolicitudId = null)
    {
        $area = Area::find($areaId);

        if (!$area) {
            return collect();
        }

        $idsDeArea = $area->users()
            ->where('activo', true)
            ->pluck('users.id')
            ->toArray();

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
            return true;
        }

        return $this->obtenerGestoresPorArea($areaId, $tipoSolicitudId)
            ->contains('id', (int) $gestorId);
    }

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

        $this->gestores = $this->obtenerGestoresPorArea($value, $this->ticket->tipo_solicitud_id);

        if (collect($this->gestores)->isEmpty()) {
            return;
        }

        $this->gestor_id = $this->resolverGestorPorDefecto($this->gestores);
    }

    public function store()
    {
        $this->authorize('ticket.accion-derivar');

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

            if (!$this->gestorPerteneceAArea($this->a_area_id, $this->gestor_id, $this->ticket->tipo_solicitud_id)) {
                $this->addError('gestor_id', 'El gestor seleccionado no pertenece al área destino.');
                DB::rollBack();
                return;
            }

            $oldArea = $this->mapAreas[$this->ticket->area_id] ?? 'N/A';
            $oldGestor = $this->ticket->gestor->name ?? 'Sin asignar';

            $newAreaName = $this->mapAreas[$this->a_area_id] ?? 'N/A';
            $newGestorName = $this->mapUsuarios[$this->gestor_id] ?? 'N/A';

            TicketDerivado::create([
                'ticket_id' => $this->ticket->id,
                'de_area_id' => $this->ticket->area_id,
                'a_area_id' => $this->a_area_id,
                'usuario_deriva_id' => Auth::id(),
                'usuario_recibe_id' => $this->gestor_id,
                'motivo' => $this->motivo,
            ]);

            $estadoDerivadoId = EstadoTicket::id(EstadoTicket::DERIVADO);

            $this->ticket->update([
                'area_id' => $this->a_area_id,
                'gestor_id' => $this->gestor_id,
                'estado_ticket_id' => $estadoDerivadoId,
                'updated_by' => Auth::id(),
            ]);

            // Registrar participantes: el que deriva y el que recibe
            $this->ticket->usuariosParticipantes()->syncWithoutDetaching([
                Auth::id(),
                (int) $this->gestor_id
            ]);

            $detalle = "Ticket derivado de Área '$oldArea' a '$newAreaName' | ";
            $detalle .= "Gestor cambiado de '$oldGestor' a '$newGestorName' | ";
            $detalle .= "Motivo: {$this->motivo}";

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => Auth::id(),
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
