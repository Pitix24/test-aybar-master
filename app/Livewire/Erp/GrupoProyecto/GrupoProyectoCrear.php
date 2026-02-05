<?php

namespace App\Livewire\Erp\GrupoProyecto;

use App\Models\GrupoProyecto;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.erp.layout-erp')]
class GrupoProyectoCrear extends Component
{
    public $nombre;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:grupo_proyectos,nombre',
            'activo' => 'required|boolean',
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
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            GrupoProyecto::create([
                'nombre' => $this->nombre,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardo correctamente.']);
            $this->reset();
            return redirect()->route('erp.grupo-proyecto.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear grupo de proyecto: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.grupo-proyecto.grupo-proyecto-crear');
    }
}
