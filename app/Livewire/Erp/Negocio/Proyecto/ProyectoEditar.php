<?php

namespace App\Livewire\Erp\Negocio\Proyecto;

use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Illuminate\Validation\ValidationException;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Proyecto')]
class ProyectoEditar extends Component
{
    public Proyecto $proyecto_model;

    public $unidad_negocio_id;
    public $grupo_proyecto_id;
    public $nombre;
    public $slin_id;
    public $activo;

    public $unidades = [];
    public $grupos = [];

    public function mount($id)
    {
        $this->authorize('proyecto.editar');
        $this->proyecto_model = Proyecto::findOrFail($id);

        $this->unidad_negocio_id = $this->proyecto_model->unidad_negocio_id;
        $this->grupo_proyecto_id = $this->proyecto_model->grupo_proyecto_id;
        $this->nombre = $this->proyecto_model->nombre;
        $this->slin_id = $this->proyecto_model->slin_id;
        $this->activo = (bool) $this->proyecto_model->activo;

        $this->unidades = UnidadNegocio::select('id', 'nombre')->orderBy('nombre')->get();
        $this->grupos = GrupoProyecto::select('id', 'nombre')->orderBy('nombre')->get();
    }

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'grupo_proyecto_id' => 'required|exists:grupo_proyectos,id',
            'nombre' => 'required|unique:proyectos,nombre,' . $this->proyecto_model->id,
            'slin_id' => 'nullable|max:100',
            'activo' => 'nullable|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'unidad_negocio_id' => 'unidad de negocio',
            'grupo_proyecto_id' => 'grupo de proyecto',
            'nombre' => 'nombre del proyecto',
            'slin_id' => 'SLIN ID',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('proyecto.editar');

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

            $this->proyecto_model->update([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'grupo_proyecto_id' => $this->grupo_proyecto_id,
                'nombre' => trim($this->nombre),
                'slin_id' => $this->slin_id ? trim($this->slin_id) : null,
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El proyecto se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('proyecto')->error("[PROYECTO] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->proyecto_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el proyecto.'
            ]);
        }
    }

    #[On('eliminarProyectoOn')]
    public function eliminarProyectoOn()
    {
        $this->authorize('proyecto.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->proyecto_model->nombre;
            $id = $this->proyecto_model->id;
            $this->proyecto_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El proyecto '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.proyecto.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('proyecto')->error("[PROYECTO] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el proyecto.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.proyecto.proyecto-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
