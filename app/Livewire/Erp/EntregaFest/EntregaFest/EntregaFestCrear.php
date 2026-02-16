<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\Cliente;
use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Entrega Fest')]
class EntregaFestCrear extends Component
{
    public $nombre, $descripcion, $codigo, $fecha_entrega;
    public $unidad_negocio_id, $proyecto_id, $cliente_id;
    public $activo = true;

    // Catálogos
    public $unidades_negocios = [];
    public $proyectos = [];
    public $clientes = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo' => 'required|string|max:50|unique:entrega_fests,codigo',
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

    public function mount()
    {
        $this->unidades_negocios = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $this->clientes = Cliente::orderBy('nombre')->limit(200)->get();

        $this->fecha_entrega = date('Y-m-d');
        // Autogenerar código sugerido
        $this->codigo = 'EF-' . date('Y') . '-' . str_pad(EntregaFest::withTrashed()->count() + 1, 3, '0', STR_PAD_LEFT);
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
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

    public function store()
    {
        $this->authorize('entrega-fest.crear');

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

            EntregaFest::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'cliente_id' => $this->cliente_id,
                'user_id' => Auth::id(),
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'codigo' => $this->codigo,
                'fecha_entrega' => $this->fecha_entrega,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'El evento se ha creado correctamente.'
            ]);

            return redirect()->route('entrega-fest.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ENTREGA-FEST] Error en Creación: ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un problema inesperado al guardar el evento.'
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
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-crear');
    }
}
