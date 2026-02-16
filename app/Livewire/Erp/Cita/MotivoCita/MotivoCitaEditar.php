<?php

namespace App\Livewire\Erp\Cita\MotivoCita;

use App\Models\MotivoCita;
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
#[Title('Editar Motivo de Cita')]
class MotivoCitaEditar extends Component
{
    public MotivoCita $motivo_model;

    public $nombre;
    public $color;
    public $icono;
    public $activo;

    public function mount($id)
    {
        $this->authorize('motivo-cita.editar');
        $this->motivo_model = MotivoCita::findOrFail($id);

        $this->nombre = $this->motivo_model->nombre;
        $this->color = $this->motivo_model->color;
        $this->icono = $this->motivo_model->icono;
        $this->activo = (bool) $this->motivo_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:motivo_citas,nombre,' . $this->motivo_model->id,
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del motivo',
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
        $this->authorize('motivo-cita.editar');

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

            $this->motivo_model->update([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El motivo de cita se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('motivo_cita')->error("[MOTIVO CITA] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->motivo_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el motivo de cita.'
            ]);
        }
    }

    #[On('eliminarMotivoCitaOn')]
    public function eliminarMotivoCitaOn()
    {
        $this->authorize('motivo-cita.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->motivo_model->nombre;
            $this->motivo_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El motivo de cita '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.motivo-cita.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('motivo_cita')->error("[MOTIVO CITA] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->motivo_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el motivo de cita.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.cita.motivo-cita.motivo-cita-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
