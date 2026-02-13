<?php

namespace App\Livewire\Atc\Ticket;

use App\Mail\TicketComunicacionMail;
use App\Models\Ticket;
use App\Models\TicketEmail as TicketEmailModel;
use App\Models\TicketArchivo;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TicketEmail extends Component
{
    public Ticket $ticket;
    public $asunto = '';
    public $mensaje = '';
    public $selectedAttachments = [];

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
        ]);

        $emailDestino = $this->ticket->email;

        if (!$emailDestino || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'El ticket no tiene un correo válido registrado.']);
            return;
        }

        try {
            // Obtener modelos de archivos para adjuntar
            $archivosAdjuntar = TicketArchivo::whereIn('id', $this->selectedAttachments)->get();

            // Enviar correo
            Mail::to($emailDestino)->send(new TicketComunicacionMail($this->ticket, $this->asunto, $this->mensaje, $archivosAdjuntar->all()));

            // Registrar en base de datos
            TicketEmailModel::create([
                'ticket_id' => $this->ticket->id,
                'emisor_id' => auth()->id(),
                'receptor_id' => $this->ticket->cliente_id,
                'asunto' => $this->asunto,
                'mensaje' => $this->mensaje,
                'enviado_at' => now(),
            ]);

            $this->reset(['mensaje', 'selectedAttachments']);
            $this->dispatch('alertaLivewire', ['title' => 'Enviado', 'text' => 'El correo ha sido enviado correctamente al cliente.']);
        } catch (\Exception $e) {
            Log::error('Error TicketEmail@enviar: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo enviar el correo. Revise la configuración de servidor de correo.']);
        }
    }

    public function render()
    {
        return view('livewire.atc.ticket.ticket-email', [
            'archivosCompatibles' => $this->ticket->archivos()->get()
        ]);
    }
}
