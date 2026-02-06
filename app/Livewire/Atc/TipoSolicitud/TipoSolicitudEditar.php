<?php

namespace App\Livewire\Atc\TipoSolicitud;

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
    public TipoSolicitud $tipoSolicitud;

    public $nombre;
    public $tiempo_solucion;
    public $activo = false;
    public $selectedAreas = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:tipo_solicituds,nombre,' . $this->tipoSolicitud->id,
            'tiempo_solucion' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
            'selectedAreas' => 'nullable|array',
            'selectedAreas.*' => 'exists:areas,id',
        ];
    }

    public function mount($id)
    {
        $this->tipoSolicitud = TipoSolicitud::with('areas')->findOrFail($id);

        $this->nombre = $this->tipoSolicitud->nombre;
        $this->tiempo_solucion = $this->tipoSolicitud->tiempo_solucion;
        $this->activo = $this->tipoSolicitud->activo;
        $this->selectedAreas = $this->tipoSolicitud->areas->pluck('id')->toArray();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->tipoSolicitud->update([
                'nombre' => $this->nombre,
                'tiempo_solucion' => $this->tiempo_solucion,
                'activo' => $this->activo,
            ]);

            $this->tipoSolicitud->areas()->sync($this->selectedAreas);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar tipo de solicitud: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarTipoSolicitudOn')]
    public function eliminarTipoSolicitudOn()
    {
        try {
            DB::beginTransaction();

            $this->tipoSolicitud->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.tipo-solicitud.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar tipo de solicitud: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        $areas = Area::orderBy('nombre')->get();
        return view('livewire.atc.tipo-solicitud.tipo-solicitud-editar', compact('areas'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
