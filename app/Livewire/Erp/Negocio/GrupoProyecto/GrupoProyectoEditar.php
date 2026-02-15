<?php

namespace App\Livewire\Erp\Negocio\GrupoProyecto;

use App\Models\GrupoProyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Grupo de Proyecto')]
class GrupoProyectoEditar extends Component
{
    public GrupoProyecto $grupo_model;
    public $nombre;
    public $activo;

    public function mount($id)
    {
        $this->authorize('grupo-proyecto.editar');
        $this->grupo_model = GrupoProyecto::findOrFail($id);
        $this->nombre = $this->grupo_model->nombre;
        $this->activo = (bool) $this->grupo_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:grupo_proyectos,nombre,' . $this->grupo_model->id,
            'activo' => 'nullable|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del grupo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('grupo-proyecto.editar');
        $this->validate();

        try {
            DB::beginTransaction();

            $this->grupo_model->update([
                'nombre' => trim($this->nombre),
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El grupo de proyecto se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('grupo_proyecto')->error("[GRUPO PROYECTO] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->grupo_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el grupo de proyecto.'
            ]);
        }
    }

    #[On('eliminarGrupoOn')]
    public function eliminarGrupoOn($id)
    {
        $this->authorize('grupo-proyecto.eliminar');

        try {
            DB::beginTransaction();

            $item = GrupoProyecto::findOrFail($id);
            $nombre = $item->nombre;
            $item->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El grupo '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.grupo-proyecto.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('grupo_proyecto')->error("[GRUPO PROYECTO] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el grupo de proyecto.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.grupo-proyecto.grupo-proyecto-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
