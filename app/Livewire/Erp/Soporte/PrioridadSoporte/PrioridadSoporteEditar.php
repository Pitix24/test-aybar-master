<?php

namespace App\Livewire\Erp\Soporte\PrioridadSoporte;

use App\Models\Erp\Soporte\PrioridadSoporte;
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
#[Title('Editar Prioridad de Soporte')]
class PrioridadSoporteEditar extends Component
{
    public PrioridadSoporte $prioridad_model;

    public $nombre;
    public $color;
    public $icono;
    public $activo;

    public function mount($id)
    {
        $this->prioridad_model = PrioridadSoporte::findOrFail($id);

        $this->nombre = $this->prioridad_model->nombre;
        $this->color = $this->prioridad_model->color;
        $this->icono = $this->prioridad_model->icono;
        $this->activo = (bool) $this->prioridad_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:prioridad_soportes,nombre,' . $this->prioridad_model->id,
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre de la prioridad',
            'color' => 'color informativo',
            'icono' => 'icono representativo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('prioridad-soporte.accion-editar');

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

            $this->prioridad_model->update([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'La prioridad de soporte se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('prioridad_soporte')->error("[PRIORIDAD SOPORTE] Error al actualizar: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la prioridad de soporte.'
            ]);
        }
    }

    #[On('eliminarPrioridadSoporteOn')]
    public function eliminarPrioridadSoporteOn()
    {
        $this->authorize('prioridad-soporte.accion-eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->prioridad_model->nombre;
            $this->prioridad_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "La prioridad de soporte '$nombre' ha sido eliminada."
            ]);

            return redirect()->route('erp.prioridad-soporte.vista.lista');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('prioridad_soporte')->error("[PRIORIDAD SOPORTE] Error al eliminar: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la prioridad de soporte.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.soporte.prioridad-soporte.prioridad-soporte-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
