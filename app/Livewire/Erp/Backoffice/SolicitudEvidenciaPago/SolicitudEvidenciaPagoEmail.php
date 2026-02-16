<?php

namespace App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago;

use App\Mail\EvidenciaPagoObservacionMail;
use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\EvidenciaPago;
use App\Models\SolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaPagoEmail as SolicitudEvidenciaPagoEmailModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class SolicitudEvidenciaPagoEmail extends Component
{
    public SolicitudEvidenciaPago $solicitud;

    #[Reactive]
    public $evidenciaId;

    public $mensaje_correo = '';
    public $soloLectura = false;

    public function mount(SolicitudEvidenciaPago $solicitud, $evidenciaId = null, $soloLectura = false)
    {
        $this->solicitud = $solicitud;
        $this->evidenciaId = $evidenciaId;
        $this->soloLectura = $soloLectura;
    }

    public function enviarCorreo($cambiarEstado = false)
    {
        abort_unless(auth()->user()->can('solicitud-evidencia-pago.editar'), 403);

        $this->validate([
            'mensaje_correo' => 'required|min:10',
        ]);

        $emailDestino = $this->solicitud->userCliente->email ?? null;

        if (!$emailDestino || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'El cliente no tiene un correo válido registrado.']);
            return;
        }

        if ($cambiarEstado && !$this->evidenciaId) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Debe seleccionar una evidencia para poder rechazarla.']);
            return;
        }

        try {
            DB::beginTransaction();

            $asunto = 'Observación de Evidencia de Pago';

            if ($cambiarEstado) {
                $asunto = 'Rechazo de Evidencia de Pago - Acción Requerida';

                $estadoRechazadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::RECHAZADO);

                $this->solicitud->update([
                    'estado_solicitud_evidencia_pago_id' => $estadoRechazadoId,
                    'usuario_valida_id' => auth()->id(),
                    'fecha_validacion' => now(),
                ]);

                $evidencia = EvidenciaPago::find($this->evidenciaId);
                if ($evidencia) {
                    $evidencia->update([
                        'estado_solicitud_evidencia_pago_id' => $estadoRechazadoId,
                        'observacion' => $this->mensaje_correo
                    ]);
                }
            }

            Mail::to($emailDestino)->send(new EvidenciaPagoObservacionMail($emailDestino, $this->solicitud, $this->mensaje_correo));

            SolicitudEvidenciaPagoEmailModel::create([
                'solicitud_evidencia_pago_id' => $this->solicitud->id,
                'emisor_id' => auth()->id(),
                'receptor_id' => $this->solicitud->cliente_id,
                'asunto' => $asunto,
                'mensaje' => $this->mensaje_correo,
                'enviado_at' => now(),
            ]);

            DB::commit();

            $this->mensaje_correo = '';
            $this->solicitud->refresh();

            $msgSuccess = $cambiarEstado ? 'El correo fue enviado y la solicitud fue marcada como RECHAZADA.' : 'El correo ha sido enviado y registrado.';
            $this->dispatch('alertaLivewire', ['title' => 'Enviado', 'text' => $msgSuccess]);

            if ($cambiarEstado) {
                $this->dispatch('solicitudActualizada');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error SolicitudEvidenciaPagoEmail@enviarCorreo: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo procesar la solicitud: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-email', [
            'correos' => $this->solicitud->correos()->with('emisor')->latest()->get(),
            'evidenciaSeleccionada' => $this->evidenciaId ? EvidenciaPago::find($this->evidenciaId) : null
        ]);
    }
}
