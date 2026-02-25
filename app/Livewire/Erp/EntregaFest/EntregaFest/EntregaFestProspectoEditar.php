<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '', $estado = '', $observacion = '';
    public $lote = '', $manzana = '';

    // BackOffice
    public $grupo, $gestor_backoffice_id = '', $fecha_culminacion_eecc, $link_carpeta_eecc, $link_eecc_firmado;
    public $validador_backoffice_id = '', $fecha_validacion_eecc, $estado_backoffice;

    // Legal
    public $estado_contrato_preeliminar_emitido, $estado_firma_contrato_firmado;
    public $fecha_firma, $fecha_generacion_contrato;

    public $proyectos = [];

    protected function rules()
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'observacion' => 'nullable|string',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',

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
    }

    protected function validationAttributes()
    {
        return [
            'proyecto_id' => 'proyecto',
            'dni' => 'DNI',
            'nombres' => 'nombres completos',
            'email' => 'correo electrónico',
            'celular' => 'número de celular',
            'estado' => 'estado del prospecto',
            'grupo' => 'grupo backoffice',
            'estado_backoffice' => 'estado backoffice',
            'estado_contrato_preeliminar_emitido' => 'estado contrato preliminar',
            'estado_firma_contrato_firmado' => 'estado firma contrato',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id, $prospectoId)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->prospecto = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)->findOrFail($prospectoId);

        $this->proyecto_id = $this->prospecto->proyecto_id;
        $this->dni = $this->prospecto->dni;
        $this->nombres = $this->prospecto->nombres;
        $this->email = $this->prospecto->email;
        $this->celular = $this->prospecto->celular;
        $this->estado = $this->prospecto->estado;
        $this->observacion = $this->prospecto->observacion;
        $this->lote = $this->prospecto->lote;
        $this->manzana = $this->prospecto->manzana;

        // BackOffice
        $this->grupo = $this->prospecto->grupo;
        $this->gestor_backoffice_id = $this->prospecto->gestor_backoffice_id;
        $this->fecha_culminacion_eecc = $this->prospecto->fecha_culminacion_eecc ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_culminacion_eecc)) : null;
        $this->link_carpeta_eecc = $this->prospecto->link_carpeta_eecc;
        $this->link_eecc_firmado = $this->prospecto->link_eecc_firmado;
        $this->validador_backoffice_id = $this->prospecto->validador_backoffice_id;
        $this->fecha_validacion_eecc = $this->prospecto->fecha_validacion_eecc ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_validacion_eecc)) : null;
        $this->estado_backoffice = $this->prospecto->estado_backoffice;

        // Legal
        $this->estado_contrato_preeliminar_emitido = $this->prospecto->estado_contrato_preeliminar_emitido;
        $this->estado_firma_contrato_firmado = $this->prospecto->estado_firma_contrato_firmado;
        $this->fecha_firma = $this->prospecto->fecha_firma ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_firma)) : null;
        $this->fecha_generacion_contrato = $this->prospecto->fecha_generacion_contrato ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_generacion_contrato)) : null;

        $this->proyectos = $this->evento->proyectos;
    }

    private function handleUpdate(array $data, string $logContext)
    {
        $this->authorize('entrega-fest.prospectos');

        try {
            DB::beginTransaction();
            $this->prospecto->update($data);
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Información actualizada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[$logContext] Error: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'prospecto_id' => $this->prospecto->id,
                'datos' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la información.'
            ]);
        }
    }

    public function updateProspecto()
    {
        $rules = [
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'observacion' => 'nullable|string',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'proyecto_id' => $this->proyecto_id,
            'dni' => $this->dni,
            'nombres' => trim($this->nombres),
            'email' => trim($this->email),
            'celular' => trim($this->celular),
            'estado' => $this->estado,
            'observacion' => $this->observacion,
            'lote' => $this->lote,
            'manzana' => $this->manzana,
        ], 'PROSPECTO EDITAR - BASICO');
    }

    public function updateBackoffice()
    {
        $rules = [
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',
            'validador_backoffice_id' => 'nullable|exists:users,id',
            'fecha_validacion_eecc' => 'nullable|date',
            'estado_backoffice' => 'required|in:pendiente,observado,aprobado,rechazado',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'grupo' => $this->grupo,
            'gestor_backoffice_id' => $this->gestor_backoffice_id ?: null,
            'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
            'link_carpeta_eecc' => $this->link_carpeta_eecc,
            'link_eecc_firmado' => $this->link_eecc_firmado,
            'validador_backoffice_id' => $this->validador_backoffice_id ?: null,
            'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
            'estado_backoffice' => $this->estado_backoffice,
        ], 'PROSPECTO EDITAR - BACKOFFICE');
    }

    public function updateLegal()
    {
        $rules = [
            'estado_contrato_preeliminar_emitido' => 'required|in:pendiente,observado,aprobado,rechazado',
            'estado_firma_contrato_firmado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'fecha_firma' => 'nullable|date',
            'fecha_generacion_contrato' => 'nullable|date',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
            'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
            'fecha_firma' => $this->fecha_firma,
            'fecha_generacion_contrato' => $this->fecha_generacion_contrato,
        ], 'PROSPECTO EDITAR - LEGAL');
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
