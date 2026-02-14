<?php

namespace App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago;

use App\Models\SolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaMensaje;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class SolicitudEvidenciaChat extends Component
{
    public SolicitudEvidenciaPago $solicitud;
    public $mensaje = '';
    public $es_interno = false;
    public $isOpen = false;

    #[On('toggleChat')]
    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('chatOpened');
        }
    }

    public function enviar()
    {
        abort_unless(auth()->user()->can('solicitud-evidencia-pago.ver'), 403);

        if (trim($this->mensaje) == '') {
            return;
        }

        try {
            SolicitudEvidenciaMensaje::create([
                'solicitud_evidencia_pago_id' => $this->solicitud->id,
                'user_id' => auth()->id(),
                'mensaje' => $this->mensaje,
                'es_interno' => $this->es_interno,
            ]);

            $this->reset(['mensaje', 'es_interno']);
            $this->dispatch('mensajeEnviado');
        } catch (\Exception $e) {
            Log::error('Error al enviar mensaje chat solicitud evidencia: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo enviar el mensaje.']);
        }
    }

    public function render()
    {
        $mensajes = SolicitudEvidenciaMensaje::where('solicitud_evidencia_pago_id', $this->solicitud->id)
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-chat', [
            'mensajes' => $mensajes
        ]);
    }
}
