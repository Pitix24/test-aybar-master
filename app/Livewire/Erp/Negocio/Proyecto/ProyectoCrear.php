<?php

namespace App\Livewire\Erp\Negocio\Proyecto;

use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Illuminate\Validation\ValidationException;

#[Lazy]
#[Title('Crear Proyecto')]
#[Layout('layouts.erp.layout-erp')]
class ProyectoCrear extends Component
{
    public $unidad_negocio_id = "";
    public $grupo_proyecto_id = "";
    public $nombre = '';
    public $slin_id = '';
    public $activo = true;

    public $unidades = [];
    public $grupos = [];

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'grupo_proyecto_id' => 'required|exists:grupo_proyectos,id',
            'nombre' => 'required|unique:proyectos,nombre',
            'slin_id' => 'nullable|max:100',
            'activo' => 'nullable|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'unidad_negocio_id' => 'unidad de negocio',
            'grupo_proyecto_id' => 'grupo de proyecto',
            'nombre' => 'nombre del proyecto',
            'slin_id' => 'SLIN ID',
            'activo' => 'estado',
        ];
    }

    public function mount()
    {
        $this->unidades = UnidadNegocio::select('id', 'nombre')->orderBy('nombre')->get();
        $this->grupos = GrupoProyecto::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('proyecto.crear');

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

            $nuevo = Proyecto::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'grupo_proyecto_id' => $this->grupo_proyecto_id,
                'nombre' => trim($this->nombre),
                'slin_id' => $this->slin_id ? trim($this->slin_id) : null,
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Proyecto creado correctamente.'
            ]);

            return redirect()->route('erp.proyecto.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('proyecto')->error("[PROYECTO] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el proyecto.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.proyecto.proyecto-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
