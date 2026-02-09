<?php

namespace App\Livewire\Erp\Proyecto;

use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Title('Crear Proyecto')]
#[Layout('layouts.erp.layout-erp')]
class ProyectoCrear extends Component
{
    public $unidad_negocios, $unidad_negocio_id = "";
    public $grupo_proyectos, $grupo_proyecto_id = "";

    public $nombre = '';
    public $slin_id = '';
    public $activo = false;

    protected function rules()
    {
        return [
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'grupo_proyecto_id' => 'required|exists:grupo_proyectos,id',
            'nombre' => 'required|unique:proyectos,nombre',
            'slin_id' => 'nullable',
            'activo' => 'required|boolean',
        ];
    }

    public function mount()
    {
        $this->unidad_negocios = UnidadNegocio::all();
        $this->grupo_proyectos = GrupoProyecto::all();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        abort_unless(auth()->user()->can('proyecto.crear'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            Proyecto::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'grupo_proyecto_id' => $this->grupo_proyecto_id,
                'nombre' => $this->nombre,
                'slin_id' => $this->slin_id ?: null,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.proyecto.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear proyecto: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.proyecto.proyecto-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
