<?php

namespace App\Livewire\Erp\Soporte\CierreSoporte;

use App\Models\Erp\Soporte\CierreSoporte;
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
#[Title('Editar Cierre de Soporte')]
class CierreSoporteEditar extends Component
{
    public CierreSoporte $cierre_model;

    public $nombre;
    public $color;
    public $icono;
    public $activo;

    public function mount($id)
    {
        $this->cierre_model = CierreSoporte::findOrFail($id);

        $this->nombre = $this->cierre_model->nombre;
        $this->color = $this->cierre_model->color;
        $this->icono = $this->cierre_model->icono;
        $this->activo = (bool) $this->cierre_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:cierre_soportes,nombre,' . $this->cierre_model->id,
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del cierre',
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
        $this->authorize('cierre-soporte.accion-editar');

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

            $this->cierre_model->update([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El cierre de soporte se actualizó correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cierre_soporte')->error("[CIERRE SOPORTE] Error al actualizar: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el cierre de soporte.'
            ]);
        }
    }

    #[On('eliminarCierreSoporteOn')]
    public function eliminarCierreSoporteOn()
    {
        $this->authorize('cierre-soporte.accion-eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->cierre_model->nombre;
            $this->cierre_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El cierre de soporte '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.cierre-soporte.vista.lista');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cierre_soporte')->error("[CIERRE SOPORTE] Error al eliminar: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el cierre de soporte.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.soporte.cierre-soporte.cierre-soporte-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
