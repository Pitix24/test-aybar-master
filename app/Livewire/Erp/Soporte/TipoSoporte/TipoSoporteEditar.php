<?php

namespace App\Livewire\Erp\Soporte\TipoSoporte;

use App\Models\Erp\Soporte\TipoSoporte;
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
#[Title('Editar Tipo de Soporte')]
class TipoSoporteEditar extends Component
{
    public TipoSoporte $tipo_model;

    public $nombre;
    public $color;
    public $icono;
    public $activo;

    public function mount($id)
    {
        $this->tipo_model = TipoSoporte::findOrFail($id);

        $this->nombre = $this->tipo_model->nombre;
        $this->color = $this->tipo_model->color;
        $this->icono = $this->tipo_model->icono;
        $this->activo = (bool) $this->tipo_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:tipo_soportes,nombre,' . $this->tipo_model->id,
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del tipo',
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
        $this->authorize('tipo-soporte.accion-editar');

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
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El tipo de soporte se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tipo_soporte')->error("[TIPO SOPORTE] Error al actualizar: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el tipo de soporte.'
            ]);
        }
    }

    #[On('eliminarTipoSoporteOn')]
    public function eliminarTipoSoporteOn()
    {
        $this->authorize('tipo-soporte.accion-eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->tipo_model->nombre;
            $this->tipo_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El tipo de soporte '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.tipo-soporte.vista.lista');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tipo_soporte')->error("[TIPO SOPORTE] Error al eliminar: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el tipo de soporte.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.soporte.tipo-soporte.tipo-soporte-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
