<?php

namespace App\Livewire\Erp\Negocio\Area;

use App\Models\Area;
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
#[Title('Editar Área')]
class AreaEditar extends Component
{
    public Area $area_model;

    public $nombre = '';
    public $email_buzon = '';
    public $color = '';
    public $icono = '';
    public $activo = false;
    public $selectedSedes = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:areas,nombre,' . $this->area_model->id,
            'email_buzon' => 'nullable|email|max:255',
            'color' => 'nullable|string|max:20',
            'icono' => 'nullable|string|max:100',
            'activo' => 'required|boolean',
            'selectedSedes' => 'nullable|array',
            'selectedSedes.*' => 'exists:sedes,id',
        ];
    }

    public function validationAttributes()
    {
        return [
            'nombre' => 'nombre del área',
            'email_buzon' => 'email de buzón',
            'selectedSedes' => 'sedes seleccionadas',
        ];
    }

    public function mount($id)
    {
        $this->area_model = Area::findOrFail($id);
        $this->nombre = $this->area_model->nombre;
        $this->email_buzon = $this->area_model->email_buzon;
        $this->color = $this->area_model->color;
        $this->icono = $this->area_model->icono;
        $this->activo = (bool) $this->area_model->activo;
        $this->selectedSedes = $this->area_model->sedes()->pluck('sedes.id')->toArray();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('area.editar');

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

            $this->area_model->update([
                'nombre' => $this->nombre,
                'email_buzon' => $this->email_buzon ?: null,
                'color' => $this->color ?: '#3b82f6',
                'icono' => $this->icono ?: 'fa-solid fa-shapes',
                'activo' => $this->activo,
            ]);

            $this->area_model->sedes()->sync($this->selectedSedes);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'El área se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('negocio')->error("[AREA] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->area_model->id,
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

    #[On('eliminarAreaOn')]
    public function eliminarAreaOn()
    {
        $this->authorize('area.eliminar');

        try {
            DB::beginTransaction();

            $id = $this->area_model->id;
            $nombre = $this->area_model->nombre;

            $this->area_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'Se eliminó correctamente.'
            ]);

            return redirect()->route('erp.area.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('negocio')->error("[AREA] Error al eliminar: " . $e->getMessage(), [
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
        $sedes = Sede::where('activo', true)->orderBy('nombre')->get();
        return view('livewire.erp.negocio.area.area-editar', compact('sedes'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
