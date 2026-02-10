<?php

namespace App\Livewire\Atc\Ticket;

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
            ->where('id', '!=', $this->ticket->area_id)
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

        $this->gestores = $area->users()
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();

        if ($this->gestores->isEmpty()) {
            return;
        }

        $principal = $this->gestores
            ->first(fn($u) => (bool) $u->pivot->is_principal);

        $this->gestor_id = $principal
            ? $principal->id
            : $this->gestores->first()->id;
    }

    public function derivar()
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        $this->validate();

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
            Log::error('Error TicketDerivar@derivar: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No se pudo procesar la derivación.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.atc.ticket.ticket-derivar', [
            'derivados' => $this->ticket->derivados()
                ->with(['deArea', 'aArea', 'usuarioDeriva', 'usuarioRecibe'])
                ->latest()
                ->get()
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
