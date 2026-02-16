<?php

namespace App\Livewire\Erp\Backoffice\SolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\Proyecto;
use App\Models\SolicitudEvidenciaPago;
use App\Models\UnidadNegocio;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Detalle de Solicitud de Evidencia')]
class SolicitudEvidenciaPagoVer extends Component
{
    public SolicitudEvidenciaPago $solicitud;

    // Campos (Modo lectura)
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

    // Evidencia seleccionada
    public $evidenciaSeleccionada;
    public $evidenciaSeleccionadaId;

    public function mount($id)
    {
        $this->solicitud = SolicitudEvidenciaPago::with([
            'evidencias.estado',
            'unidadNegocio',
            'proyecto',
            'userCliente.perfilCliente',
            'estado',
            'gestor',
            'correos.emisor',
            'usuarioValida'
        ])->findOrFail($id);

        $this->unidad_negocio_id = $this->solicitud->unidad_negocio_id;
        $this->proyecto_id = $this->solicitud->proyecto_id;
        $this->gestor_id = $this->solicitud->gestor_id;
        $this->estado_id = $this->solicitud->estado_solicitud_evidencia_pago_id;
        $this->observacion = $this->solicitud->observacion;

        $this->unidades_negocios = UnidadNegocio::where('activo', true)->get();
        $this->estados = EstadoSolicitudEvidenciaPago::where('activo', true)->get();
        $this->gestores = User::role(['asesor-atc', 'supervisor-atc'])->get();

        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->get();
        }
    }

    public function seleccionarEvidencia($evidenciaId)
    {
        $this->evidenciaSeleccionada = $this->solicitud->evidencias->firstWhere('id', $evidenciaId);
        $this->evidenciaSeleccionadaId = $evidenciaId;
    }

    public function render()
    {
        return view('livewire.erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-ver');
    }
}
