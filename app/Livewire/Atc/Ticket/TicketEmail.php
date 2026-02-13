<?php

namespace App\Livewire\Atc\Ticket;

use App\Mail\TicketComunicacionMail;
use App\Models\Ticket;
use App\Models\TicketEmail as TicketEmailModel;
use App\Models\TicketArchivo;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use Livewire\WithFileUploads;

class TicketEmail extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public $asunto = '';
    public $mensaje = '';
    public $selectedAttachments = [];
    public $nuevosArchivos = [];

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->asunto = "Información sobre su Ticket #{$ticket->id}";
    }

    public function enviar()
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        $this->validate([
            'asunto' => 'required|min:5|max:200',
            'mensaje' => 'required|min:10',
            'selectedAttachments' => 'nullable|array',
            'nuevosArchivos.*' => 'nullable|file|max:10240', // 10MB c/u
        ]);

        $emailDestino = $this->ticket->email;

        if (!$emailDestino || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'El ticket no tiene un correo válido registrado.']);
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $archivosAdjuntarOriginal = [];

            // 1. Archivos existentes seleccionados
            if (!empty($this->selectedAttachments)) {
                $archivosAdjuntarOriginal = TicketArchivo::whereIn('id', $this->selectedAttachments)->get()->all();
            }

            // 2. Procesar nuevos archivos cargados (se guardan en el ticket también)
            if (!empty($this->nuevosArchivos)) {
                foreach ($this->nuevosArchivos as $file) {
                    $path = $file->store('tickets/' . $this->ticket->id . '/emails', 'public');

                    $nuevoArchivo = TicketArchivo::create([
                        'archivable_type' => Ticket::class,
                        'archivable_id' => $this->ticket->id,
                        'user_id' => auth()->id(),
                        'nombre_original' => $file->getClientOriginalName(),
                        'path' => $path,
                        'url' => \Illuminate\Support\Facades\Storage::url($path),
                        'descripcion' => 'Adjunto enviado por email: ' . $this->asunto,
                        'extension' => $file->getClientOriginalExtension(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);

                    $archivosAdjuntarOriginal[] = $nuevoArchivo;
                }
            }

            // Enviar correo
            Mail::to($emailDestino)->send(new TicketComunicacionMail($this->ticket, $this->asunto, $this->mensaje, $archivosAdjuntarOriginal));

            // Registrar en base de datos
            TicketEmailModel::create([
                'ticket_id' => $this->ticket->id,
                'emisor_id' => auth()->id(),
                'receptor_id' => $this->ticket->cliente_id,
                'asunto' => $this->asunto,
                'mensaje' => $this->mensaje,
                'enviado_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            $this->reset(['mensaje', 'selectedAttachments', 'nuevosArchivos']);
            $this->dispatch('alertaLivewire', ['title' => 'Enviado', 'text' => 'El correo ha sido enviado correctamente al cliente.']);

            // Emitir evento para refrescar la lista de archivos si fuera necesario
            $this->dispatch('archivoSubido');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error('Error TicketEmail@enviar: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->dispatch('alertaLivewire', [
                'title' => 'Error de Envío',
                'text' => 'Hubo un problema técnico: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.atc.ticket.ticket-email', [
            'archivosCompatibles' => $this->ticket->archivos()->get()
        ]);
    }
}
