<?php

namespace App\Livewire\Erp\Negocio\GrupoProyecto;

use App\Models\GrupoProyecto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Grupo de Proyecto')]
class GrupoProyectoCrear extends Component
{
    public $nombre;
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:grupo_proyectos,nombre',
            'activo' => 'nullable|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del grupo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('grupo-proyecto.crear');

        $this->validate();

        try {
            DB::beginTransaction();

            $nuevo = GrupoProyecto::create([
                'nombre' => trim($this->nombre),
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Grupo de proyecto creado correctamente.'
            ]);

            return redirect()->route('erp.grupo-proyecto.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('grupo_proyecto')->error("[GRUPO PROYECTO] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el grupo de proyecto.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.grupo-proyecto.grupo-proyecto-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
