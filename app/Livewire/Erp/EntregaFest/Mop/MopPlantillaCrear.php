<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFestMopPlantilla;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Plantilla MOP')]
class MopPlantillaCrear extends Component
{
    public $rol_nombre = '';
    public $fase = 'ANTES';
    public $instruccion = '';
    public $prioridad = 1;

    protected function rules()
    {
        return [
            'rol_nombre' => 'required|string|max:100',
            'fase' => 'required|in:ANTES,DURANTE,CIERRE',
            'instruccion' => 'required|string',
            'prioridad' => 'required|integer|min:1',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'rol_nombre' => 'rol / cargo',
            'fase' => 'fase del evento',
            'instruccion' => 'instrucción',
            'prioridad' => 'prioridad',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
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

            EntregaFestMopPlantilla::create([
                'rol_nombre' => trim($this->rol_nombre),
                'fase' => $this->fase,
                'instruccion' => trim($this->instruccion),
                'prioridad' => $this->prioridad,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'Plantilla MOP creada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.mop.plantillas');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP PLANTILLA CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear la plantilla.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.mop.mop-plantilla-crear');
    }
}
