<?php

namespace App\Livewire\Backoffice\SolicitudEvidenciaPago;

use App\Mail\EvidenciaPagoObservacionMail;
use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\EvidenciaPago;
use App\Models\Proyecto;
use App\Models\SolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaPagoEmail;
use App\Models\UnidadNegocio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Solicitud de Evidencia')]
class SolicitudEvidenciaPagoEditar extends Component
{
    public SolicitudEvidenciaPago $solicitud;

    // Campos editables
    public $unidad_negocio_id;
    public $proyecto_id;
    public $gestor_id;
    public $estado_id;
    public $observacion;

    // Catálogos
    public $unidades_negocios = [];
    public $proyectos = [];
    public $gestores = [];
    public $estados = [];

    // Evidencia seleccionada para validar
    public $evidenciaSeleccionada;
    public $evidenciaSeleccionadaId;

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'gestor_id' => 'nullable|exists:users,id',
            'estado_id' => 'required|exists:estado_solicitud_evidencia_pagos,id',
            'observacion' => 'nullable|string',
        ];
    }

    #[On('solicitudActualizada')]
    public function refreshSolicitud()
    {
        $this->solicitud->refresh();
        $this->estado_id = $this->solicitud->estado_solicitud_evidencia_pago_id;
    }

    public function mount($id)
    {
        $this->solicitud = SolicitudEvidenciaPago::with([
            'evidencias.estado',
            'unidadNegocio',
            'proyecto',
            'userCliente.perfilCliente',
            'estado',
            'gestor',
            'correos.emisor'
        ])->findOrFail($id);

        $this->unidad_negocio_id = $this->solicitud->unidad_negocio_id;
        $this->proyecto_id = $this->solicitud->proyecto_id;
        $this->gestor_id = $this->solicitud->gestor_id;
        $this->estado_id = $this->solicitud->estado_solicitud_evidencia_pago_id;
        $this->observacion = $this->solicitud->observacion;

        $this->unidades_negocios = UnidadNegocio::where('activo', true)->get();
        $this->estados = EstadoSolicitudEvidenciaPago::where('activo', true)->get();
        $this->gestores = User::role(['asesor-atc', 'supervisor-atc'])->get();

        $this->loadProyectos();
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = null;
        $this->loadProyectos();
    }

    public function loadProyectos()
    {
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->get();
        } else {
            $this->proyectos = [];
        }
    }

    public function update()
    {
        abort_unless(auth()->user()->can('solicitud-evidencia-pago.editar'), 403);

        $this->validate();

        try {
            $this->solicitud->update([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'gestor_id' => $this->gestor_id,
                'estado_solicitud_evidencia_pago_id' => $this->estado_id,
                'observacion' => $this->observacion,
            ]);

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Datos actualizados correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error SolicitudEvidenciaPagoEditar@update: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudieron guardar los cambios.']);
        }
    }

    public function seleccionarEvidencia($evidenciaId)
    {
        $this->evidenciaSeleccionada = $this->solicitud->evidencias->firstWhere('id', $evidenciaId);
        $this->evidenciaSeleccionadaId = $evidenciaId;

        if ($this->evidenciaSeleccionada) {
            if ($this->solicitud->monto_operacion == $this->evidenciaSeleccionada->monto) {
                session()->flash('success_evidencia', '¡El monto coincide con la cuota!');
            } else {
                session()->flash('info_evidencia', 'El monto no coincide con la cuota.');
            }
        }
    }

    public function enviarSlin()
    {
        abort_unless(auth()->user()->can('solicitud-evidencia-pago.editar'), 403);

        if (!$this->evidenciaSeleccionada) {
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Debe seleccionar una evidencia primero.']);
            return;
        }

        if (!$this->solicitud->slin_asbanc) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Esta solicitud no es de tipo ASBANC.']);
            return;
        }

        if (!Storage::disk('public')->exists($this->evidenciaSeleccionada->path)) {
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se encontró el archivo físico de la evidencia.']);
            return;
        }

        try {
            $imageContent = Storage::disk('public')->get($this->evidenciaSeleccionada->path);
            $fechaOperacion = Carbon::parse($this->evidenciaSeleccionada->fecha)->format('m/d/Y');

            $params = [
                'empresa' => (string) ($this->solicitud->unidadNegocio->slin_id ?? ''),
                'lote' => (string) ($this->solicitud->lote_completo ?? ''),
                'cliente' => (string) ($this->solicitud->codigo_cliente ?? ''),
                'contrato' => '',
                'idcobranzas' => (string) ($this->solicitud->transaccion_id ?? ''),
                'base64Image' => base64_encode($imageContent),
                'nrooperacion' => (string) $this->evidenciaSeleccionada->numero_operacion,
                'fechaoperacion' => $fechaOperacion,
                'mtooperacion' => (string) $this->evidenciaSeleccionada->monto,
            ];

            $response = Http::acceptJson()
                ->contentType('application/json')
                ->timeout(30)
                ->post(config('services.slin.url') ?? 'https://aybarcorp.com/api/slin/guardar-evidencia', $params);

            $body = $response->json();

            if ($response->failed() || (isset($body['data']['success']) && $body['data']['success'] === false)) {
                $mensaje = $body['data']['message'] ?? 'Error de comunicación con el servicio SLIN.';

                $estadoRechazadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::RECHAZADO);

                $this->solicitud->update([
                    'estado_solicitud_evidencia_pago_id' => $estadoRechazadoId,
                    'usuario_valida_id' => auth()->id(),
                ]);

                $this->evidenciaSeleccionada->update([
                    'estado_solicitud_evidencia_pago_id' => $estadoRechazadoId,
                    'slin_respuesta' => $mensaje,
                ]);

                $this->estado_id = $estadoRechazadoId;
                $this->dispatch('alertaLivewire', ['title' => 'Rechazado por SLIN', 'text' => $mensaje]);
                return;
            }

            $estadoAprobadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::APROBADO);

            $this->solicitud->update([
                'estado_solicitud_evidencia_pago_id' => $estadoAprobadoId,
                'slin_evidencia' => true,
                'usuario_valida_id' => auth()->id(),
                'fecha_validacion' => now(),
            ]);

            $this->evidenciaSeleccionada->update([
                'estado_solicitud_evidencia_pago_id' => $estadoAprobadoId,
                'slin_respuesta' => $body['data']['message'] ?? 'Procesado correctamente por SLIN.',
            ]);

            $this->estado_id = $estadoAprobadoId;
            $this->solicitud->refresh();
            $this->evidenciaSeleccionada->refresh();

            $this->dispatch('alertaLivewire', ['title' => 'Éxito', 'text' => 'Evidencia enviada y validada por SLIN.']);

        } catch (\Exception $e) {
            Log::error('Error SolicitudEvidenciaPagoEditar@enviarSlin: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al procesar con SLIN.']);
        }
    }

    public function cerrarManual()
    {
        abort_unless(auth()->user()->can('solicitud-evidencia-pago.editar'), 403);

        if (!$this->evidenciaSeleccionada) {
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Debe seleccionar una evidencia primero.']);
            return;
        }

        try {
            $estadoAprobadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::APROBADO);

            $this->solicitud->update([
                'estado_solicitud_evidencia_pago_id' => $estadoAprobadoId,
                'resuelto_manual' => true,
                'usuario_valida_id' => auth()->id(),
                'fecha_validacion' => now(),
            ]);

            $this->evidenciaSeleccionada->update([
                'estado_solicitud_evidencia_pago_id' => $estadoAprobadoId,
            ]);

            $this->estado_id = $estadoAprobadoId;
            $this->solicitud->refresh();
            $this->evidenciaSeleccionada->refresh();

            $this->dispatch('alertaLivewire', ['title' => 'Aprobado', 'text' => 'La solicitud ha sido cerrada manualmente con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error SolicitudEvidenciaPagoEditar@cerrarManual: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo realizar el cierre manual.']);
        }
    }

    public function render()
    {
        return view('livewire.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
