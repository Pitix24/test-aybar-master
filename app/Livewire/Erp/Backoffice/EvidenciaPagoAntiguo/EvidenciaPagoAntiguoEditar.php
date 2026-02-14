<?php

namespace App\Livewire\Erp\Backoffice\EvidenciaPagoAntiguo;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\EvidenciaPagoAntiguo;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Evidencia Pago Stock')]
class EvidenciaPagoAntiguoEditar extends Component
{
    use AuthorizesRequests;

    public EvidenciaPagoAntiguo $evidencia;
    public $estado_id;
    public $observacion;
    public $unidad_negocio_id;
    public $proyecto_id;
    public $gestor_id;

    public $estados;
    public $empresas;
    public $proyectos = [];
    public $gestores = [];

    protected function rules()
    {
        return [
            'estado_id' => 'required',
            'unidad_negocio_id' => 'required',
            'proyecto_id' => 'required',
            'gestor_id' => 'nullable',
            'observacion' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->evidencia = EvidenciaPagoAntiguo::findOrFail($id);
        $this->estado_id = $this->evidencia->estado_solicitud_evidencia_pago_id;
        $this->observacion = $this->evidencia->observacion;
        $this->unidad_negocio_id = $this->evidencia->unidad_negocio_id;
        $this->proyecto_id = $this->evidencia->proyecto_id;
        $this->gestor_id = $this->evidencia->gestor_id ?? '';

        $this->estados = EstadoSolicitudEvidenciaPago::all();
        $this->empresas = UnidadNegocio::all();
        $this->loadProyectos();

        $this->gestores = User::role(['asesor-atc', 'supervisor-atc'])
            ->get();
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        if ($value) {
            $this->loadProyectos();
        }
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
        abort_unless(auth()->user()->can('evidencia-pago-antiguo.editar'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->evidencia->update([
                'estado_solicitud_evidencia_pago_id' => $this->estado_id,
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'gestor_id' => $this->gestor_id ?: null,
                'observacion' => $this->observacion,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar evidencia stock: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar.']);
        }
    }

    public function validar()
    {
        abort_unless(auth()->user()->can('evidencia-pago-validar'), 403);

        try {
            DB::beginTransaction();

            $estadoAprobadoId = EstadoSolicitudEvidenciaPago::id(EstadoSolicitudEvidenciaPago::APROBADO);

            $this->evidencia->update([
                'estado_solicitud_evidencia_pago_id' => $estadoAprobadoId,
                'usuario_valida_id' => auth()->id(),
                'fecha_validacion' => now(),
            ]);

            DB::commit();

            $this->estado_id = $estadoAprobadoId;
            $this->evidencia->refresh();

            $this->dispatch('alertaLivewire', [
                'title' => 'Validado',
                'text' => 'Se validó correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al validar evidencia stock: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo validar.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.backoffice.evidencia-pago-antiguo.evidencia-pago-antiguo-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
