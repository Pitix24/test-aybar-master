<?php

namespace App\Livewire\Erp\Atc\TipoSolicitud;

use App\Models\Area;
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
#[Title('Editar Tipo de Solicitud')]
class TipoSolicitudEditar extends Component
{
    public TipoSolicitud $tipo_model;

    public $nombre;
    public $tiempo_solucion;
    public $activo;
    public $selectedAreas = [];

    public function mount($id)
    {
        $this->authorize('tipo-solicitud.editar');
        $this->tipo_model = TipoSolicitud::with('areas')->findOrFail($id);

        $this->nombre = $this->tipo_model->nombre;
        $this->tiempo_solucion = $this->tipo_model->tiempo_solucion;
        $this->activo = (bool) $this->tipo_model->activo;
        $this->selectedAreas = $this->tipo_model->areas->pluck('id')->toArray();
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:tipo_solicituds,nombre,' . $this->tipo_model->id,
            'tiempo_solucion' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
            'selectedAreas' => 'nullable|array',
            'selectedAreas.*' => 'exists:areas,id',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del tipo de solicitud',
            'tiempo_solucion' => 'tiempo de solución',
            'activo' => 'estado',
            'selectedAreas' => 'áreas vinculadas',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('tipo-solicitud.editar');

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

            $this->tipo_model->update([
                'nombre' => trim($this->nombre),
                'tiempo_solucion' => $this->tiempo_solucion,
                'activo' => $this->activo ?? false,
            ]);

            $this->tipo_model->areas()->sync($this->selectedAreas);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El tipo de solicitud se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tipo_solicitud')->error("[TIPO SOLICITUD] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->tipo_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el tipo de solicitud.'
            ]);
        }
    }

    #[On('eliminarTipoSolicitudOn')]
    public function eliminarTipoSolicitudOn()
    {
        $this->authorize('tipo-solicitud.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->tipo_model->nombre;
            $this->tipo_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El tipo de solicitud '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.tipo-solicitud.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tipo_solicitud')->error("[TIPO SOLICITUD] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->tipo_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el tipo de solicitud.'
            ]);
        }
    }

    public function render()
    {
        $areas = Area::select('id', 'nombre')->orderBy('nombre')->get();
        return view('livewire.erp.atc.tipo-solicitud.tipo-solicitud-editar', compact('areas'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
