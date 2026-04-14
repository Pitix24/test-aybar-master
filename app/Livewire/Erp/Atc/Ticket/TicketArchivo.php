<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\TicketArchivo as TicketArchivoModel;
use App\Models\TicketHistorial;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TicketArchivo extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public $archivo;
    public $descripcion_archivo;
    public $archivos_existentes = [];
    public $soloLectura = false;

    public function mount(Ticket $ticket, $soloLectura = false)
    {
        $this->ticket = $ticket;
        $this->soloLectura = $soloLectura;
        $this->refreshArchivos();
    }

    public function refreshArchivos()
    {
        $this->archivos_existentes = $this->ticket->archivos()->get();
    }

    public function adjuntar()
    {
        $this->authorize('ticket.accion-agregar-archivo');

        try {
            $this->validate([
                'archivo' => 'required|file|max:51200|mimes:pdf,docx,xlsx,pptx,jpg,jpeg,png',
                'descripcion_archivo' => 'required|min:3|max:200',
            ]);
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

            $filename = $this->archivo->getClientOriginalName();
            $extension = $this->archivo->getClientOriginalExtension();
            $path = $this->archivo->store('tickets/' . $this->ticket->id, 'public');

            TicketArchivoModel::create([
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

            // Registrar al usuario que adjunta el archivo como participante
            $this->ticket->usuariosParticipantes()->syncWithoutDetaching([auth()->id()]);

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Adjunto',
                'detalle' => "Se añadió el archivo: '{$this->descripcion_archivo}' ({$filename})",
            ]);

            DB::commit();

            $this->reset(['archivo', 'descripcion_archivo']);
            $this->refreshArchivos();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Adjunto',
                'text' => 'Archivo subido con éxito.'
            ]);
            $this->dispatch('archivoSubido'); // Avisar a otros componentes (como el de Email)
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error TicketArchivo@adjuntar: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo subir el archivo.'
            ]);
        }
    }

    #[On('eliminarArchivoOn')]
    public function eliminarArchivo($archivoId)
    {
        $this->authorize('ticket.accion-eliminar-archivo');

        try {
            DB::beginTransaction();
            $archivo = TicketArchivoModel::findOrFail($archivoId);

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

            DB::commit();

            $this->refreshArchivos();
            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Archivo eliminado.']);
            $this->dispatch('archivoSubido'); // Refrescar otros componentes
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error TicketArchivo@eliminarArchivo: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar el archivo.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-archivo');
    }
}
