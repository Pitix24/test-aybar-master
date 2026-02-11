<?php

namespace App\Livewire\Cita\MotivoCita;

use App\Models\MotivoCita;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
class MotivoCitaEditar extends Component
{
    public MotivoCita $motivo;

    public $nombre;
    public $icono;
    public $color;
    public $activo;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:motivo_citas,nombre,' . $this->motivo->id,
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->motivo = MotivoCita::findOrFail($id);

        $this->nombre = $this->motivo->nombre;
        $this->icono = $this->motivo->icono;
        $this->color = $this->motivo->color;
        $this->activo = (bool) $this->motivo->activo;
    }

    public function store()
    {
        abort_unless(auth()->user()->can('motivo-cita.editar'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores en el formulario.']);
            throw $e;
        }

        $this->motivo->update([
            'nombre' => $this->nombre,
            'icono' => $this->icono,
            'color' => $this->color,
            'activo' => $this->activo,
        ]);

        $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'El motivo de cita ha sido actualizado.']);
    }

    #[On('eliminarMotivoCitaOn')]
    public function eliminarMotivoCitaOn()
    {
        abort_unless(auth()->user()->can('motivo-cita.eliminar'), 403);

        if ($this->motivo->citas()->exists()) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'No se puede eliminar un motivo que tiene citas asociadas.']);
            return;
        }

        $this->motivo->delete();
        $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El motivo ha sido eliminado.']);
        return redirect()->route('erp.motivo-cita.vista.todo');
    }

    public function render()
    {
        return view('livewire.cita.motivo-cita.motivo-cita-editar');
    }
}
