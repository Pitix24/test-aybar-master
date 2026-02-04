<?php

namespace App\Livewire\Erp\GrupoProyecto;

use App\Models\GrupoProyecto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

#[Layout('layouts.erp.layout-erp')]
class GrupoProyectoEditar extends Component
{
    public GrupoProyecto $grupoProyecto;
    public $nombre = '';
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:grupo_proyectos,nombre,' . $this->grupoProyecto->id,
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->grupoProyecto = GrupoProyecto::findOrFail($id);
        $this->nombre = $this->grupoProyecto->nombre;
        $this->activo = $this->grupoProyecto->activo;
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

            $this->grupoProyecto->update([
                'nombre' => $this->nombre,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizo correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar unidad de negocio: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarGrupoProyectoOn')]
    public function eliminarGrupoProyectoOn()
    {
        if ($this->grupoProyecto) {
            $this->grupoProyecto->delete();

            return redirect()->route('erp.grupo-proyecto.vista.todo');
        }
    }

    public function render()
    {
        return view('livewire.erp.grupo-proyecto.grupo-proyecto-editar');
    }
}
