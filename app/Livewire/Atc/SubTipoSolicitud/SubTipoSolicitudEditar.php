<?php

namespace App\Livewire\Atc\SubTipoSolicitud;

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
    public SubTipoSolicitud $subTipoSolicitud;

    public $tipos_solicitud;
    public $tipo_solicitud_id = "";
    public $nombre;
    public $tiempo_solucion;
    public $activo = false;

    protected function rules()
    {
        return [
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',
            'nombre' => 'required|unique:sub_tipo_solicituds,nombre,' . $this->subTipoSolicitud->id,
            'tiempo_solucion' => 'nullable|numeric|min:0',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->subTipoSolicitud = SubTipoSolicitud::findOrFail($id);

        $this->tipos_solicitud = TipoSolicitud::where('activo', true)->get();

        $this->tipo_solicitud_id = $this->subTipoSolicitud->tipo_solicitud_id;
        $this->nombre = $this->subTipoSolicitud->nombre;
        $this->tiempo_solucion = $this->subTipoSolicitud->tiempo_solucion;
        $this->activo = $this->subTipoSolicitud->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        abort_unless(auth()->user()->can('sub-tipo-solicitud.editar'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->subTipoSolicitud->update([
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'nombre' => $this->nombre,
                'tiempo_solucion' => $this->tiempo_solucion ?: null,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar sub-tipo de solicitud: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarSubTipoSolicitudOn')]
    public function eliminarSubTipoSolicitudOn()
    {
        abort_unless(auth()->user()->can('sub-tipo-solicitud.eliminar'), 403);
        try {
            DB::beginTransaction();

            $this->subTipoSolicitud->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.sub-tipo-solicitud.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar sub-tipo de solicitud: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.atc.sub-tipo-solicitud.sub-tipo-solicitud-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
