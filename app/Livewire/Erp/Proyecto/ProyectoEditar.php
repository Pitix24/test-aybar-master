<?php

namespace App\Livewire\Erp\Proyecto;

use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp')]
class ProyectoEditar extends Component
{
    public Proyecto $proyecto;

    public $unidad_negocios, $unidad_negocio_id = "";
    public $grupo_proyectos, $grupo_proyecto_id = "";

    public $nombre;
    public $slin_id;
    public $activo = false;
    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'grupo_proyecto_id' => 'required|exists:grupo_proyectos,id',
            'nombre' => 'required|unique:proyectos,nombre,' . $this->proyecto->id,
            'slin_id' => 'nullable|string|max:255',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->proyecto = Proyecto::findOrFail($id);

        $this->unidad_negocios = UnidadNegocio::all();
        $this->grupo_proyectos = GrupoProyecto::all();

        $this->unidad_negocio_id = $this->proyecto->unidad_negocio_id;
        $this->grupo_proyecto_id = $this->proyecto->grupo_proyecto_id;
        $this->nombre = $this->proyecto->nombre;
        $this->slin_id = $this->proyecto->slin_id;
        $this->activo = $this->proyecto->activo;
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

            $this->proyecto->update([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'grupo_proyecto_id' => $this->grupo_proyecto_id,
                'nombre' => $this->nombre,
                'slin_id' => $this->slin_id,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar proyecto: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarProyectoOn')]
    public function eliminarProyectoOn()
    {
        try {
            DB::beginTransaction();

            $this->proyecto->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.proyecto.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar proyecto: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.proyecto.proyecto-editar');
    }
}
