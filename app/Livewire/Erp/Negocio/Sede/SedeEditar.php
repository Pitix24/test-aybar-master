<?php

namespace App\Livewire\Erp\Negocio\Sede;

use App\Models\Sede;
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
#[Title('Editar Sede')]
class SedeEditar extends Component
{
    public Sede $sede_model;

    public $nombre = '';
    public $direccion = '';
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:sedes,nombre,' . $this->sede_model->id,
            'direccion' => 'nullable|string|max:500',
            'activo' => 'required|boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'nombre' => 'nombre de la sede',
            'direccion' => 'dirección',
        ];
    }

    public function mount($id)
    {
        $this->sede_model = Sede::findOrFail($id);
        $this->nombre = $this->sede_model->nombre;
        $this->direccion = $this->sede_model->direccion;
        $this->activo = (bool) $this->sede_model->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('sede.editar');

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

            $this->sede_model->update([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion ?: null,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'La sede se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('sede')->error("[SEDE] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->sede_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo completar la operación.'
            ]);
        }
    }

    #[On('eliminarSedeOn')]
    public function eliminarSedeOn()
    {
        $this->authorize('sede.eliminar');

        try {
            DB::beginTransaction();

            $id = $this->sede_model->id;
            $nombre = $this->sede_model->nombre;

            $this->sede_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'Se eliminó correctamente.'
            ]);

            return redirect()->route('erp.sede.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('sede')->error("[SEDE] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $id ?? null,
                'nombre' => $nombre ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.sede.sede-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
