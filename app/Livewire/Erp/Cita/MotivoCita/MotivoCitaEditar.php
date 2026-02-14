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
    public MotivoCita $motivoCita;

    public $nombre;
    public $color;
    public $icono;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:motivo_citas,nombre,' . $this->motivoCita->id,
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->motivoCita = MotivoCita::findOrFail($id);

        $this->nombre = $this->motivoCita->nombre;
        $this->color = $this->motivoCita->color;
        $this->icono = $this->motivoCita->icono;
        $this->activo = $this->motivoCita->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        abort_unless(auth()->user()->can('motivo-cita.editar'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->motivoCita->update([
                'nombre' => $this->nombre,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar motivo de cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarMotivoCitaOn')]
    public function eliminarMotivoCitaOn()
    {
        abort_unless(auth()->user()->can('motivo-cita.eliminar'), 403);
        try {
            DB::beginTransaction();

            $this->motivoCita->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.motivo-cita.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar motivo de cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
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
