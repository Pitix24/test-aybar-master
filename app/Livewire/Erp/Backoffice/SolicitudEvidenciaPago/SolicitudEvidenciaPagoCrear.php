<?php

namespace App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\SolicitudEvidenciaPago;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Solicitud de Evidencia')]
class SolicitudEvidenciaPagoCrear extends Component
{
    public $unidad_negocio_id = '';
    public $proyecto_id = '';
    public $gestor_id = '';
    public $estado_id = '';
    public $observacion = '';

    public $unidades_negocios = [];
    public $proyectos = [];
    public $gestores = [];
    public $estados = [];

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

    public function mount()
    {
        $this->unidades_negocios = UnidadNegocio::where('activo', true)->get();
        $this->estados = EstadoSolicitudEvidenciaPago::where('activo', true)->get();
        $this->gestores = User::role(['asesor-atc', 'supervisor-atc'])->get();

        // Estado por defecto: PENDIENTE
        $this->estado_id = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::PENDIENTE);
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        if ($value) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $value)->get();
        } else {
            $this->proyectos = [];
        }
    }

    public function store()
    {
        $this->authorize('solicitud-evidencia-pago.crear');

        try {
            $this->validate();
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

            $solicitud = SolicitudEvidenciaPago::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'gestor_id' => $this->gestor_id,
                'estado_solicitud_evidencia_pago_id' => $this->estado_id,
                'observacion' => $this->observacion,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => '¡Creado!', 'text' => 'La solicitud se creó correctamente.']);

            return redirect()->route('erp.solicitud-evidencia-pago.vista.editar', $solicitud->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('solicitud_evidencia_pago')->error("[SOLICITUD EVIDENCIA PAGO] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo crear la solicitud.']);
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        return view('livewire.erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-crear');
    }
}
