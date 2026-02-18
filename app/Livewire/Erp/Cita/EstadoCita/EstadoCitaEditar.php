<?php

namespace App\Livewire\Erp\Cita\EstadoCita;

use App\Models\EstadoCita;
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
#[Title('Editar Estado de Cita')]
class EstadoCitaEditar extends Component
{
    public EstadoCita $estado_model;

    public $nombre;
    public $color;
    public $icono;
    public $activo;

    public function mount($id)
    {
        $this->estado_model = EstadoCita::findOrFail($id);

        $this->nombre = $this->estado_model->nombre;
        $this->color = $this->estado_model->color;
        $this->icono = $this->estado_model->icono;
        $this->activo = (bool) $this->estado_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:estado_citas,nombre,' . $this->estado_model->id,
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del estado',
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
        $this->authorize('estado-cita.editar');

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

            $this->estado_model->update([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El estado de cita se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('estado_cita')->error("[ESTADO CITA] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->estado_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado de cita.'
            ]);
        }
    }

    #[On('eliminarEstadoCitaOn')]
    public function eliminarEstadoCitaOn()
    {
        $this->authorize('estado-cita.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->estado_model->nombre;
            $this->estado_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El estado de cita '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.estado-cita.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('estado_cita')->error("[ESTADO CITA] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->estado_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el estado de cita.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.cita.estado-cita.estado-cita-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
