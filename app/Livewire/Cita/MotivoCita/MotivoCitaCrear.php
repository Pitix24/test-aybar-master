<?php

namespace App\Livewire\Cita\MotivoCita;

use App\Models\MotivoCita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Motivo de Cita')]
class MotivoCitaCrear extends Component
{
    public $nombre = '';
    public $color = '#64748b';
    public $icono = 'fa-solid fa-circle-info';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:motivo_citas,nombre',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        abort_unless(auth()->user()->can('motivo-cita.crear'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            MotivoCita::create([
                'nombre' => $this->nombre,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.motivo-cita.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear motivo de cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.cita.motivo-cita.motivo-cita-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
