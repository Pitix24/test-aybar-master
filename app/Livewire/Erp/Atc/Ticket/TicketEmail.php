<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Mail\TicketComunicacionMail;
use App\Models\Ticket;
use App\Models\TicketEmail as TicketEmailModel;
use App\Models\TicketArchivo;
use App\Models\TicketHistorial;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TicketEmail extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public $asunto = '';
    public $mensaje = '';
    public $nuevosArchivos = [];
    public $soloLectura = false;

    public function mount(Ticket $ticket, $soloLectura = false)
    {
        $this->ticket = $ticket;
        $this->soloLectura = $soloLectura;
        $this->asunto = "Información sobre su Ticket #{$ticket->id}";
    }

    public function quitarArchivo($index)
    {
        unset($this->nuevosArchivos[$index]);
        $this->nuevosArchivos = array_values($this->nuevosArchivos);
    }

    public function store()
    {
        $this->authorize('ticket.enviar-correo');

        try {
            $this->validate([
                'asunto' => 'required|min:5|max:200',
                'mensaje' => 'required|min:10',
                'nuevosArchivos.*' => 'nullable|file|max:10240', // 10MB c/u
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        $emailDestino = $this->ticket->email;

        if (!$emailDestino || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'El ticket no tiene un correo válido registrado.']);
            return;
        }

        try {
            DB::beginTransaction();

            $archivosAdjuntar = [];

            // Procesar nuevos archivos cargados (se guardan en el ticket también)
            if (!empty($this->nuevosArchivos)) {
                foreach ($this->nuevosArchivos as $file) {
                    $path = $file->store('tickets/' . $this->ticket->id . '/emails', 'public');

                    $nuevoArchivo = TicketArchivo::create([
                        'archivable_type' => Ticket::class,
                        'archivable_id' => $this->ticket->id,
                        'user_id' => auth()->id(),
                        'nombre_original' => $file->getClientOriginalName(),
                        'path' => $path,
                        'url' => Storage::url($path),
                        'descripcion' => 'Adjunto enviado por email: ' . $this->asunto,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);

                    $archivosAdjuntar[] = $nuevoArchivo;
                }
            }

            // Enviar correo
            Mail::to($emailDestino)->send(new TicketComunicacionMail($this->ticket, $this->asunto, $this->mensaje, $archivosAdjuntar));

            // Registrar en base de datos
            TicketEmailModel::create([
                'ticket_id' => $this->ticket->id,
                'emisor_id' => auth()->id(),
                'receptor_id' => $this->ticket->cliente_id,
                'asunto' => $this->asunto,
                'mensaje' => $this->mensaje,
                'enviado_at' => now(),
            ]);

            // Registrar al usuario que envía el correo como participante
            $this->ticket->usuariosParticipantes()->syncWithoutDetaching([auth()->id()]);

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Envío de Email',
                'detalle' => "Comunicación enviada al cliente. Asunto: '{$this->asunto}'",
            ]);

            DB::commit();

            $this->reset(['mensaje', 'nuevosArchivos']);
            $this->dispatch('alertaLivewire', ['title' => 'Enviado', 'text' => 'El correo ha sido enviado correctamente al cliente.']);

            // Emitir evento para refrescar la lista de archivos si fuera necesario
            $this->dispatch('archivoSubido');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error TicketEmail@store: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'title' => 'Error de Envío',
                'text' => 'Hubo un problema técnico: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-email', [
            'correos' => TicketEmailModel::where('ticket_id', $this->ticket->id)
                ->with('emisor')
                ->latest()
                ->get()
        ]);
    }
}
