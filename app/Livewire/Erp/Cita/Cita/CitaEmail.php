<?php

namespace App\Livewire\Erp\Cita\Cita;

use App\Mail\CitaComunicacionMail;
use App\Models\Cita;
use App\Models\CitaEmail as CitaEmailModel;
use App\Models\CitaArchivo;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CitaEmail extends Component
{
    use WithFileUploads;

    public Cita $cita;
    public $asunto = '';
    public $mensaje = '';
    public $nuevosArchivos = [];
    public $soloLectura = false;

    public function mount(Cita $cita, $soloLectura = false)
    {
        $this->cita = $cita;
        $this->soloLectura = $soloLectura;
        $this->asunto = "Información sobre su Cita #{$cita->id}";
    }

    public function quitarArchivo($index)
    {
        unset($this->nuevosArchivos[$index]);
        $this->nuevosArchivos = array_values($this->nuevosArchivos);
    }

    public function store()
    {
        $this->authorize('cita.enviar-correo');

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

        // Determinar email de destino
        $emailDestino = $this->cita->ticket->email ?? $this->cita->userCliente?->email ?? null;

        if (!$emailDestino || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'La cita no tiene un correo válido registrado.']);
            return;
        }

        try {
            DB::beginTransaction();

            $archivosAdjuntar = [];

            // Procesar nuevos archivos cargados
            if (!empty($this->nuevosArchivos)) {
                foreach ($this->nuevosArchivos as $file) {
                    $path = $file->store('citas/' . $this->cita->id . '/emails', 'public');

                    $nuevoArchivo = CitaArchivo::create([
                        'cita_id' => $this->cita->id,
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
            Mail::to($emailDestino)->send(new CitaComunicacionMail($this->cita, $this->asunto, $this->mensaje, $archivosAdjuntar));

            // Registrar en base de datos
            CitaEmailModel::create([
                'cita_id' => $this->cita->id,
                'emisor_id' => auth()->id(),
                'receptor_id' => $this->cita->cliente_id,
                'asunto' => $this->asunto,
                'mensaje' => $this->mensaje,
                'enviado_at' => now(),
            ]);

            DB::commit();

            $this->reset(['mensaje', 'nuevosArchivos']);
            $this->dispatch('alertaLivewire', ['title' => 'Enviado', 'text' => 'El correo ha sido enviado correctamente al cliente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cita')->error('[CITA] Error CitaEmail@store: ' . $e->getMessage(), [
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
        return view('livewire.erp.cita.cita.cita-email', [
            'correos' => CitaEmailModel::where('cita_id', $this->cita->id)
                ->with('emisor')
                ->latest()
                ->get(),
            'emailDestino' => $this->cita->ticket->email ?? $this->cita->userCliente?->email ?? 'No registrado'
        ]);
    }
}
