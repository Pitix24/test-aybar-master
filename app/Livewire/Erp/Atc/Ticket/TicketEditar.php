<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Models\EstadoTicket;
use App\Models\TicketHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Ticket')]
class TicketEditar extends Component
{
    public Ticket $ticket;

    // Campos editables
    public $email;
    public $celular;
    public $estado_ticket_id;
    public $asunto_respuesta;
    public $descripcion_respuesta;

    public $modalHijosMasivos = false;

    // Catálogos y datos para UI
    public $mapEstados = [];

    protected function rules()
    {
        return [
            'email' => 'nullable|email|max:150',
            'celular' => 'nullable|string|max:50',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
            'asunto_respuesta' => 'nullable|string|max:255',
            'descripcion_respuesta' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::with(['hijos', 'padre.gestor', 'usuariosParticipantes', 'userCliente'])->findOrFail($id);

        $this->email = $this->ticket->email;
        $this->celular = $this->ticket->celular;
        $this->estado_ticket_id = $this->ticket->estado_ticket_id;
        $this->asunto_respuesta = $this->ticket->asunto_respuesta;
        $this->descripcion_respuesta = $this->ticket->descripcion_respuesta;

        $this->mapEstados = EstadoTicket::pluck('nombre', 'id')->toArray();
    }

    public function validationAttributes()
    {
        return [
            'email' => 'Correo Electrónico',
            'celular' => 'Número de Celular',
            'estado_ticket_id' => 'Estado del Ticket',
            'asunto_respuesta' => 'Asunto de Respuesta',
            'descripcion_respuesta' => 'Descripción de Respuesta',
        ];
    }

    public function update()
    {
        $this->authorize('ticket.accion-editar');

        // Validar y, si hay errores, notificar pero NO relanzar la excepción
        // para que Livewire pueda pintar los errores por campo en la vista.
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();

            // Enviar alerta al frontend
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);

            // Añadir cada error al error bag de Livewire para que se resalten los campos
            foreach ($errors as $field => $messages) {
                $this->addError($field, implode(' | ', $messages));
            }

            // Registrar para diagnóstico en logs de ticket
            Log::channel('ticket')->warning('[TICKET] Validación fallida en edición', [
                'ticket_id' => $this->ticket->id ?? null,
                'usuario_id' => auth()->id(),
                'errors' => $errors,
            ]);

            return; // detener ejecución
        }

        try {
            DB::beginTransaction();

            $old = $this->ticket->fresh();
            $cambios = [];

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

            if (trim($this->asunto_respuesta ?? '') !== trim($old->asunto_respuesta ?? '')) {
                $cambios[] = "Asunto respuesta: " . ($this->asunto_respuesta ?? '(vacío)');
            }

            if (trim($this->descripcion_respuesta ?? '') !== trim($old->descripcion_respuesta ?? '')) {
                $cambios[] = "Descripción respuesta: " . ($this->descripcion_respuesta ?? '(vacío)');
            }

            $this->ticket->update([
                'email' => $this->email,
                'celular' => $this->celular,
                'estado_ticket_id' => $this->estado_ticket_id,
                'asunto_respuesta' => $this->asunto_respuesta,
                'descripcion_respuesta' => $this->descripcion_respuesta,
                'updated_by' => auth()->id(),
            ]);

            // Registrar al usuario que edita como participante
            $this->ticket->usuariosParticipantes()->syncWithoutDetaching([auth()->id()]);

            if (!empty($cambios)) {
                TicketHistorial::create([
                    'ticket_id' => $this->ticket->id,
                    'user_id' => auth()->id(),
                    'accion' => 'Edición',
                    'detalle' => implode(" | ", $cambios),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'Cambios guardados correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error en Edición: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'ticket_id' => $this->ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudieron guardar los cambios. Intente nuevamente.'
            ]);
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        $this->authorize('ticket.accion-eliminar');

        try {
            if ($this->ticket->hijos()->exists()) {
                $this->dispatch('alertaLivewire', [
                    'type' => 'warning',
                    'title' => 'No permitido',
                    'text' => 'Este ticket tiene tickets hijos asociados y no puede ser eliminado.'
                ]);
                return;
            }

            $ticket_id = $this->ticket->id;
            $this->ticket->delete();

            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            Log::channel('ticket')->error('[TICKET] Error en Eliminación: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'ticket_id' => $this->ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el ticket.'
            ]);
        }
    }

    public function abrirHijosMasivos()
    {
        $this->modalHijosMasivos = true;
    }

    #[On('cerrarModalHijosMasivos')]
    public function cerrarHijosMasivos()
    {
        $this->modalHijosMasivos = false;
    }

    #[On('refreshHijos')]
    public function refreshHijos()
    {
        $this->ticket->load('hijos');
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-editar', [
            'estados' => EstadoTicket::where('activo', true)->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
