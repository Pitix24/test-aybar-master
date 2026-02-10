<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Models\EstadoTicket;
use App\Models\TicketArchivo;
use App\Models\TicketHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\WithFileUploads;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class TicketEditar extends Component
{
    use WithFileUploads;

    public Ticket $ticket;

    // Campos editables
    public $email;
    public $celular;
    public $estado_ticket_id;

    // Participantes
    public $searchUser = '';
    public $selectedParticipants = [];

    // Archivos
    public $archivo;
    public $descripcion_archivo;
    public $archivos_existentes = [];

    // Catálogos y datos para UI
    public $mapEstados = [];

    protected function rules()
    {
        return [
            'email' => 'nullable|email|max:150',
            'celular' => 'nullable|string|max:50',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
            'selectedParticipants' => 'nullable|array',
            'selectedParticipants.*' => 'exists:users,id',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::with(['hijos', 'usuariosParticipantes', 'cliente'])->findOrFail($id);

        $this->email = $this->ticket->email;
        $this->celular = $this->ticket->celular;
        $this->estado_ticket_id = $this->ticket->estado_ticket_id;

        $this->selectedParticipants = $this->ticket->usuariosParticipantes()->pluck('users.id')->toArray();
        $this->archivos_existentes = $this->ticket->archivos()->get();

        $this->mapEstados = EstadoTicket::pluck('nombre', 'id')->toArray();
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
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        $this->validate();

        try {
            DB::beginTransaction();

            $old = $this->ticket->fresh();
            $cambios = [];

            // 1. Detectar cambios para el historial
            if ($this->estado_ticket_id != $old->estado_ticket_id) {
                $viejo = $this->mapEstados[$old->estado_ticket_id] ?? 'N/A';
                $nuevo = $this->mapEstados[$this->estado_ticket_id] ?? 'N/A';
                $cambios[] = "Estado cambiado de '$viejo' a '$nuevo'";
            }

            if ($this->email != $old->email) {
                $cambios[] = "Correo actualizado de '" . ($old->email ?? 'vacío') . "' a '{$this->email}'";
            }

            if ($this->celular != $old->celular) {
                $cambios[] = "Celular actualizado de '" . ($old->celular ?? 'vacío') . "' a '{$this->celular}'";
            }

            // 2. Actualizar Ticket
            $this->ticket->update([
                'email' => $this->email,
                'celular' => $this->celular,
                'estado_ticket_id' => $this->estado_ticket_id,
                'updated_by' => auth()->id(),
            ]);

            // 3. Sincronizar participantes
            $this->ticket->usuariosParticipantes()->sync($this->selectedParticipants);

            // 4. Registrar historial si hay cambios significativos
            if (!empty($cambios)) {
                TicketHistorial::create([
                    'ticket_id' => $this->ticket->id,
                    'user_id' => auth()->id(),
                    'accion' => 'Edición',
                    'detalle' => implode(" | ", $cambios),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Cambios guardados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TicketEditar@store: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudieron guardar los cambios.']);
        }
    }

    public function adjuntar()
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        $this->validate([
            'archivo' => 'required|file|max:51200|mimes:pdf,docx,xlsx,pptx,jpg,jpeg,png',
            'descripcion_archivo' => 'required|min:3|max:200',
        ]);

        try {
            DB::beginTransaction();

            $filename = $this->archivo->getClientOriginalName();
            $extension = $this->archivo->getClientOriginalExtension();
            $path = $this->archivo->store('tickets/' . $this->ticket->id, 'public');

            TicketArchivo::create([
                'archivable_type' => Ticket::class,
                'archivable_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'nombre_original' => $filename,
                'path' => $path,
                'url' => Storage::url($path),
                'descripcion' => $this->descripcion_archivo,
                'extension' => $extension,
                'size' => $this->archivo->getSize(),
                'mime_type' => $this->archivo->getMimeType(),
            ]);

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Adjunto',
                'detalle' => "Se añadió el archivo: '{$this->descripcion_archivo}' ({$filename})",
            ]);

            DB::commit();

            $this->reset(['archivo', 'descripcion_archivo']);
            $this->archivos_existentes = $this->ticket->archivos()->get();

            $this->dispatch('alertaLivewire', ['title' => 'Adjunto', 'text' => 'Archivo subido con éxito.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error TicketEditar@adjuntar: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo subir el archivo.']);
        }
    }

    #[On('eliminarArchivoOn')]
    public function eliminarArchivo($archivoId)
    {
        abort_unless(auth()->user()->can('ticket.eliminar'), 403);

        try {
            $archivo = TicketArchivo::findOrFail($archivoId);

            if (Storage::disk('public')->exists($archivo->path)) {
                Storage::disk('public')->delete($archivo->path);
            }

            $desc = $archivo->descripcion;
            $name = $archivo->nombre_original;
            $archivo->delete();

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Eliminar adjunto',
                'detalle' => "Se eliminó el archivo: '{$desc}' ({$name})",
            ]);

            $this->archivos_existentes = $this->ticket->archivos()->get();
            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Archivo eliminado.']);
        } catch (\Exception $e) {
            Log::error('Error TicketEditar@eliminarArchivo: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar el archivo.']);
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        abort_unless(auth()->user()->can('ticket.eliminar'), 403);

        try {
            if ($this->ticket->hijos()->exists()) {
                $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Este ticket tiene hijos, no se puede eliminar.']);
                return;
            }

            $this->ticket->delete();
            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            Log::error('Error TicketEditar@eliminarTicketOn: ' . $e->getMessage());
        }
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

        return view('livewire.atc.ticket.ticket-editar', [
            'estados' => EstadoTicket::where('activo', true)->get(),
            'participantesSeleccionados' => User::whereIn('id', $this->selectedParticipants)->get(),
            'participantesDisponibles' => $participantesDisponibles,
            'historial' => $this->ticket->historial()->with('usuarioHistorial')->latest()->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
