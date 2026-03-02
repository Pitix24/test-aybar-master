<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFestMopPlantilla;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Plantilla MOP')]
class MopPlantillaEditar extends Component
{
    public EntregaFestMopPlantilla $plantilla;

    public $rol_nombre = '';
    public $fase = 'ANTES';
    public $instruccion = '';
    public $prioridad = 1;

    protected function rules()
    {
        return [
            'rol_nombre' => 'required|string|max:100',
            'fase' => 'required|in:ANTES,DURANTE,CIERRE',
            'instruccion' => 'required|string',
            'prioridad' => 'required|integer|min:1',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'rol_nombre' => 'rol / cargo',
            'fase' => 'fase del evento',
            'instruccion' => 'instrucción',
            'prioridad' => 'prioridad',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->plantilla = EntregaFestMopPlantilla::findOrFail($id);
        $this->rol_nombre = $this->plantilla->rol_nombre;
        $this->fase = $this->plantilla->fase;
        $this->instruccion = $this->plantilla->instruccion;
        $this->prioridad = $this->plantilla->prioridad;
    }

    public function update()
    {
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

            $this->plantilla->update([
                'rol_nombre' => trim($this->rol_nombre),
                'fase' => $this->fase,
                'instruccion' => trim($this->instruccion),
                'prioridad' => $this->prioridad,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Plantilla MOP actualizada correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP PLANTILLA EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la plantilla.'
            ]);
        }
    }

    #[On('eliminarPlantillaOn')]
    public function eliminarPlantillaOn()
    {
        try {
            DB::beginTransaction();

            $this->plantilla->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Plantilla eliminada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.staff.mop.plantillas');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP PLANTILLA ELIMINAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la plantilla.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.mop.mop-plantilla-editar');
    }
}
