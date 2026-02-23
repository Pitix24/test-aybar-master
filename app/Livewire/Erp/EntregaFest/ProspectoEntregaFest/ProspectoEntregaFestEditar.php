<?php

namespace App\Livewire\Erp\EntregaFest\ProspectoEntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Evaluar Prospecto - Entrega Fest')]
class ProspectoEntregaFestEditar extends Component
{
    public $prospectoId;
    public $entrega_fest_id, $proyecto_id, $dni, $nombre, $apellidos, $estado, $observacion;
    public $codigo_cliente, $codigo_cuota, $lote, $manzana, $etapa;

    // BackOffice
    public $grupo, $gestor_backoffice_id, $fecha_culminacion_eecc, $link_carpeta_eecc, $link_eecc_firmado;
    public $validador_backoffice_id, $fecha_validacion_eecc, $estado_backoffice;

    // Legal
    public $estado_contrato_preeliminar_emitido, $estado_firma_contrato_firmado;
    public $fecha_firma, $fecha_generacion_contrato;

    public $proyectos = [];

    protected $rules = [
        'entrega_fest_id' => 'required|exists:entrega_fests,id',
        'proyecto_id' => 'required|exists:proyectos,id',
        'dni' => 'required|string|max:15',
        'nombre' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
        'observacion' => 'nullable|string',
        'codigo_cliente' => 'nullable|string|max:50',
        'codigo_cuota' => 'nullable|string|max:50',
        'lote' => 'nullable|string|max:20',
        'manzana' => 'nullable|string|max:20',
        'etapa' => 'nullable|string|max:50',

        // BackOffice
        'grupo' => 'required|in:A,B,C,D',
        'gestor_backoffice_id' => 'nullable|exists:users,id',
        'fecha_culminacion_eecc' => 'nullable|date',
        'link_carpeta_eecc' => 'nullable|string|max:255',
        'link_eecc_firmado' => 'nullable|string|max:255',
        'validador_backoffice_id' => 'nullable|exists:users,id',
        'fecha_validacion_eecc' => 'nullable|date',
        'estado_backoffice' => 'required|in:pendiente,observado,aprobado,rechazado',

        // Legal
        'estado_contrato_preeliminar_emitido' => 'required|in:pendiente,observado,aprobado,rechazado',
        'estado_firma_contrato_firmado' => 'required|in:pendiente,observado,aprobado,rechazado',
        'fecha_firma' => 'nullable|date',
        'fecha_generacion_contrato' => 'nullable|date',
    ];

    public function mount($id)
    {
        $prospecto = ProspectoEntregaFest::findOrFail($id);
        $this->prospectoId = $prospecto->id;
        $this->entrega_fest_id = $prospecto->entrega_fest_id;
        $this->proyecto_id = $prospecto->proyecto_id;
        $this->dni = $prospecto->dni;
        $this->nombre = $prospecto->nombre;
        $this->apellidos = $prospecto->apellidos;
        $this->estado = $prospecto->estado;
        $this->observacion = $prospecto->observacion;
        $this->codigo_cliente = $prospecto->codigo_cliente;
        $this->codigo_cuota = $prospecto->codigo_cuota;
        $this->lote = $prospecto->lote;
        $this->manzana = $prospecto->manzana;
        $this->etapa = $prospecto->etapa;

        // BackOffice
        $this->grupo = $prospecto->grupo;
        $this->gestor_backoffice_id = $prospecto->gestor_backoffice_id;
        $this->fecha_culminacion_eecc = $prospecto->fecha_culminacion_eecc;
        $this->link_carpeta_eecc = $prospecto->link_carpeta_eecc;
        $this->link_eecc_firmado = $prospecto->link_eecc_firmado;
        $this->validador_backoffice_id = $prospecto->validador_backoffice_id;
        $this->fecha_validacion_eecc = $prospecto->fecha_validacion_eecc;
        $this->estado_backoffice = $prospecto->estado_backoffice;

        // Legal
        $this->estado_contrato_preeliminar_emitido = $prospecto->estado_contrato_preeliminar_emitido;
        $this->estado_firma_contrato_firmado = $prospecto->estado_firma_contrato_firmado;
        $this->fecha_firma = $prospecto->fecha_firma;
        $this->fecha_generacion_contrato = $prospecto->fecha_generacion_contrato;

        $this->loadProyectos();
    }

    public function updatedEntregaFestId()
    {
        $this->proyecto_id = '';
        $this->loadProyectos();
    }

    public function loadProyectos()
    {
        if ($this->entrega_fest_id) {
            $evento = EntregaFest::find($this->entrega_fest_id);
            $this->proyectos = $evento ? $evento->proyectos : [];
        } else {
            $this->proyectos = [];
        }
    }

    public function update()
    {
        $this->validate();

        $prospecto = ProspectoEntregaFest::findOrFail($this->prospectoId);
        $prospecto->update([
            'entrega_fest_id' => $this->entrega_fest_id,
            'proyecto_id' => $this->proyecto_id,
            'dni' => $this->dni,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'codigo_cliente' => $this->codigo_cliente,
            'codigo_cuota' => $this->codigo_cuota,
            'lote' => $this->lote,
            'manzana' => $this->manzana,
            'etapa' => $this->etapa,
            'estado' => $this->estado,
            'observacion' => $this->observacion,

            // BackOffice
            'grupo' => $this->grupo,
            'gestor_backoffice_id' => $this->gestor_backoffice_id,
            'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
            'link_carpeta_eecc' => $this->link_carpeta_eecc,
            'link_eecc_firmado' => $this->link_eecc_firmado,
            'validador_backoffice_id' => $this->validador_backoffice_id,
            'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
            'estado_backoffice' => $this->estado_backoffice,

            // Legal
            'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
            'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
            'fecha_firma' => $this->fecha_firma,
            'fecha_generacion_contrato' => $this->fecha_generacion_contrato,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'Prospecto actualizado y evaluado correctamente.'
        ]);
    }

    public function render()
    {
        $eventos = EntregaFest::where('activo', true)->orderBy('fecha_entrega', 'desc')->get();
        $usuarios = \App\Models\User::orderBy('name')->get();
        return view('livewire.erp.entrega-fest.prospecto-entrega-fest.prospecto-entrega-fest-editar', [
            'eventos' => $eventos,
            'usuarios' => $usuarios
        ]);
    }
}
