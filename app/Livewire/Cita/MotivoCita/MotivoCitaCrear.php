<?php

namespace App\Livewire\Cita\MotivoCita;

use App\Models\MotivoCita;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
class MotivoCitaCrear extends Component
{
    public $nombre;
    public $icono = 'fa-solid fa-tag';
    public $color = '#64748b';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:motivo_citas,nombre',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function store()
    {
        abort_unless(auth()->user()->can('motivo-cita.crear'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores en el formulario.']);
            throw $e;
        }

        MotivoCita::create([
            'nombre' => $this->nombre,
            'icono' => $this->icono,
            'color' => $this->color,
            'activo' => $this->activo,
        ]);

        $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'El motivo de cita ha sido guardado correctamente.']);

        return redirect()->route('erp.motivo-cita.vista.todo');
    }

    public function render()
    {
        return view('livewire.cita.motivo-cita.motivo-cita-crear');
    }
}
