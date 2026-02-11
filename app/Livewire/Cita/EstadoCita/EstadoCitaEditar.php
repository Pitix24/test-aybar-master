<?php

namespace App\Livewire\Cita\EstadoCita;

use App\Models\EstadoCita;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
class EstadoCitaEditar extends Component
{
    public EstadoCita $estado;

    public $nombre;
    public $icono;
    public $color;
    public $activo;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:estado_citas,nombre,' . $this->estado->id,
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->estado = EstadoCita::findOrFail($id);

        $this->nombre = $this->estado->nombre;
        $this->icono = $this->estado->icono;
        $this->color = $this->estado->color;
        $this->activo = (bool) $this->estado->activo;
    }

    public function store()
    {
        abort_unless(auth()->user()->can('estado-cita.editar'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores en el formulario.']);
            throw $e;
        }

        $this->estado->update([
            'nombre' => $this->nombre,
            'icono' => $this->icono,
            'color' => $this->color,
            'activo' => $this->activo,
        ]);

        $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'El estado de cita ha sido actualizado.']);
    }

    #[On('eliminarEstadoCitaOn')]
    public function eliminarEstadoCitaOn()
    {
        abort_unless(auth()->user()->can('estado-cita.eliminar'), 403);

        if ($this->estado->citas()->exists()) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'No se puede eliminar un estado que tiene citas asociadas.']);
            return;
        }

        $this->estado->delete();
        $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El estado ha sido eliminado.']);
        return redirect()->route('erp.estado-cita.vista.todo');
    }

    public function render()
    {
        return view('livewire.cita.estado-cita.estado-cita-editar');
    }
}
