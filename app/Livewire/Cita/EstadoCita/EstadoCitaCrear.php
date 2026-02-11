<?php

namespace App\Livewire\Cita\EstadoCita;

use App\Models\EstadoCita;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
class EstadoCitaCrear extends Component
{
    public $nombre;
    public $icono = 'fa-solid fa-circle';
    public $color = '#64748b';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:estado_citas,nombre',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function store()
    {
        abort_unless(auth()->user()->can('estado-cita.crear'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores en el formulario.']);
            throw $e;
        }

        EstadoCita::create([
            'nombre' => $this->nombre,
            'icono' => $this->icono,
            'color' => $this->color,
            'activo' => $this->activo,
        ]);

        $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'El estado de cita ha sido guardado correctamente.']);

        return redirect()->route('erp.estado-cita.vista.todo');
    }

    public function render()
    {
        return view('livewire.cita.estado-cita.estado-cita-crear');
    }
}
