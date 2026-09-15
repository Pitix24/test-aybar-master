<?php

namespace App\Livewire\Erp\Atc\SubTipoSolicitud;

use App\Models\SubTipoSolicitud;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Sub Tipo de Solicitud')]
class SubTipoSolicitudEditar extends Component
{
    public SubTipoSolicitud $sub_tipo_model;

    public $tipo_solicitud_id = "";
    public $nombre;
    public $tiempo_solucion;
    public $activo;

    public $tipos = [];

    public function mount($id)
    {
        $this->sub_tipo_model = SubTipoSolicitud::findOrFail($id);

        $this->tipo_solicitud_id = $this->sub_tipo_model->tipo_solicitud_id;
        $this->nombre = $this->sub_tipo_model->nombre;
        $this->tiempo_solucion = $this->sub_tipo_model->tiempo_solucion;
        $this->activo = (bool) $this->sub_tipo_model->activo;

        $this->tipos = TipoSolicitud::select('id', 'nombre')->orderBy('nombre')->get();
    }

    protected function rules()
    {
        return [
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',
            'nombre' => 'required|unique:sub_tipo_solicituds,nombre,' . $this->sub_tipo_model->id,
            'tiempo_solucion' => 'nullable|numeric|min:0',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'tipo_solicitud_id' => 'tipo de solicitud',
            'nombre' => 'nombre del sub tipo de solicitud',
            'tiempo_solucion' => 'tiempo de solución',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('sub-tipo-solicitud.editar');

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

            $this->sub_tipo_model->update([
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'nombre' => trim($this->nombre),
                'tiempo_solucion' => $this->tiempo_solucion ?: null,
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El sub tipo de solicitud se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('sub_tipo_solicitud')->error("[SUB TIPO SOLICITUD] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->sub_tipo_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el sub tipo de solicitud.'
            ]);
        }
    }

    #[On('eliminarSubTipoSolicitudOn')]
    public function eliminarSubTipoSolicitudOn()
    {
        $this->authorize('sub-tipo-solicitud.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->sub_tipo_model->nombre;
            $this->sub_tipo_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El sub tipo de solicitud '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.sub-tipo-solicitud.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('sub_tipo_solicitud')->error("[SUB TIPO SOLICITUD] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->sub_tipo_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el sub tipo de solicitud.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.sub-tipo-solicitud.sub-tipo-solicitud-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
