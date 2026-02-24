<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Evaluar Prospecto - Entrega Fest')]
class EntregaFestProspectoEditar extends Component
{
    public EntregaFest $evento;
    public ProspectoEntregaFest $prospecto;

    // Campos del prospecto
    public $proyecto_id, $dni, $nombre, $apellidos, $estado, $observacion;
    public $codigo_cliente, $codigo_cuota, $lote, $manzana, $etapa;

    // BackOffice
    public $grupo, $gestor_backoffice_id, $fecha_culminacion_eecc, $link_carpeta_eecc, $link_eecc_firmado;
    public $validador_backoffice_id, $fecha_validacion_eecc, $estado_backoffice;

    // Legal
    public $estado_contrato_preeliminar_emitido, $estado_firma_contrato_firmado;
    public $fecha_firma, $fecha_generacion_contrato;

    public $proyectos = [];

    protected $rules = [
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

    public function mount($id, $prospectoId)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->prospecto = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)->findOrFail($prospectoId);

        $this->proyecto_id = $this->prospecto->proyecto_id;
        $this->dni = $this->prospecto->dni;
        $this->nombre = $this->prospecto->nombre;
        $this->apellidos = $this->prospecto->apellidos;
        $this->estado = $this->prospecto->estado;
        $this->observacion = $this->prospecto->observacion;
        $this->codigo_cliente = $this->prospecto->codigo_cliente;
        $this->codigo_cuota = $this->prospecto->codigo_cuota;
        $this->lote = $this->prospecto->lote;
        $this->manzana = $this->prospecto->manzana;
        $this->etapa = $this->prospecto->etapa;

        // BackOffice
        $this->grupo = $this->prospecto->grupo;
        $this->gestor_backoffice_id = $this->prospecto->gestor_backoffice_id;
        $this->fecha_culminacion_eecc = $this->prospecto->fecha_culminacion_eecc;
        $this->link_carpeta_eecc = $this->prospecto->link_carpeta_eecc;
        $this->link_eecc_firmado = $this->prospecto->link_eecc_firmado;
        $this->validador_backoffice_id = $this->prospecto->validador_backoffice_id;
        $this->fecha_validacion_eecc = $this->prospecto->fecha_validacion_eecc;
        $this->estado_backoffice = $this->prospecto->estado_backoffice;

        // Legal
        $this->estado_contrato_preeliminar_emitido = $this->prospecto->estado_contrato_preeliminar_emitido;
        $this->estado_firma_contrato_firmado = $this->prospecto->estado_firma_contrato_firmado;
        $this->fecha_firma = $this->prospecto->fecha_firma;
        $this->fecha_generacion_contrato = $this->prospecto->fecha_generacion_contrato;

        $this->proyectos = $this->evento->proyectos;
    }

    public function update()
    {
        $this->validate();

        $this->prospecto->update([
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
            'grupo' => $this->grupo,
            'gestor_backoffice_id' => $this->gestor_backoffice_id,
            'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
            'link_carpeta_eecc' => $this->link_carpeta_eecc,
            'link_eecc_firmado' => $this->link_eecc_firmado,
            'validador_backoffice_id' => $this->validador_backoffice_id,
            'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
            'estado_backoffice' => $this->estado_backoffice,
            'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
            'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
            'fecha_firma' => $this->fecha_firma,
            'fecha_generacion_contrato' => $this->fecha_generacion_contrato,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'Prospecto actualizado correctamente.'
        ]);
    }

    public function render()
    {
        $usuarios = User::orderBy('name')->get();
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-prospecto-editar', [
            'usuarios' => $usuarios
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
