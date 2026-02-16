<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\Cliente;
use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Entrega Fest')]
class EntregaFestEditar extends Component
{
    public EntregaFest $evento;

    // Campos del formulario
    public $nombre, $descripcion, $codigo, $fecha_entrega;
    public $unidad_negocio_id, $proyecto_id, $cliente_id;
    public $activo;

    // Catálogos
    public $unidades_negocios = [];
    public $proyectos = [];
    public $clientes = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo' => 'required|string|max:50|unique:entrega_fests,codigo,' . $this->evento->id,
            'fecha_entrega' => 'required|date',
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'activo' => 'boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'unidad_negocio_id' => 'Unidad de Negocio',
            'proyecto_id' => 'Proyecto',
            'cliente_id' => 'Cliente Responsable',
            'fecha_entrega' => 'Fecha de Entrega',
            'codigo' => 'Código Único',
            'nombre' => 'Nombre del Evento'
        ];
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['unidadNegocio', 'proyecto', 'cliente'])->findOrFail($id);

        // Carga de datos iniciales
        $this->nombre = $this->evento->nombre;
        $this->descripcion = $this->evento->descripcion;
        $this->codigo = $this->evento->codigo;
        $this->fecha_entrega = $this->evento->fecha_entrega->format('Y-m-d');
        $this->unidad_negocio_id = $this->evento->unidad_negocio_id;
        $this->proyecto_id = $this->evento->proyecto_id;
        $this->cliente_id = $this->evento->cliente_id;
        $this->activo = $this->evento->activo;

        // Carga de catálogos
        $this->unidades_negocios = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $this->clientes = Cliente::orderBy('nombre')->limit(200)->get();

        $this->loadProyectos();
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        $this->proyectos = [];
        if ($value) {
            $this->loadProyectos();
        }
    }

    public function loadProyectos()
    {
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();
        }
    }

    public function update()
    {
        $this->authorize('entrega-fest.editar');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los campos obligatorios.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->evento->update([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'cliente_id' => $this->cliente_id,
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'codigo' => $this->codigo,
                'fecha_entrega' => $this->fecha_entrega,
                'activo' => $this->activo,
                'updated_by' => auth()->id() // Asumiendo que añadimos esta columna o similar
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Los cambios se han guardado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ENTREGA-FEST] Error en Edición: ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el evento. Intente nuevamente.'
            ]);
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-editar');
    }
}
