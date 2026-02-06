<?php

namespace App\Livewire\Erp\Area;

use App\Models\Area;
use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class AreaEditar extends Component
{
    public Area $area;

    public $nombre;
    public $email_buzon;
    public $color;
    public $icono;
    public $activo = false;
    public $selectedSedes = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:areas,nombre,' . $this->area->id,
            'email_buzon' => 'nullable|email',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
            'selectedSedes' => 'nullable|array',
            'selectedSedes.*' => 'exists:sedes,id',
        ];
    }

    public function mount($id)
    {
        $this->area = Area::findOrFail($id);

        $this->nombre = $this->area->nombre;
        $this->email_buzon = $this->area->email_buzon;
        $this->color = $this->area->color;
        $this->icono = $this->area->icono;
        $this->activo = $this->area->activo;
        $this->selectedSedes = $this->area->sedes()->pluck('sedes.id')->toArray();
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

            $this->area->update([
                'nombre' => $this->nombre,
                'email_buzon' => $this->email_buzon,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            $this->area->sedes()->sync($this->selectedSedes);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar área: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarAreaOn')]
    public function eliminarAreaOn()
    {
        try {
            DB::beginTransaction();

            $this->area->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.area.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar área: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        $sedes = Sede::where('activo', true)->orderBy('nombre')->get();
        return view('livewire.erp.area.area-editar', compact('sedes'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
