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
    public $unidad_negocio_id, $cliente_id;
    public $proyecto_id = ""; // Para el select
    public $proyectos_agregados = []; // Para la tabla
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
            'proyectos_agregados' => 'required|array|min:1',
            'proyectos_agregados.*.id' => 'exists:proyectos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'activo' => 'boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'unidad_negocio_id' => 'Unidad de Negocio',
            'proyectos_agregados' => 'Proyectos del Evento',
            'cliente_id' => 'Responsable (Cliente)',
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
        $this->proyectos_agregados = [];
        $this->proyecto_id = "";
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

    public function agregarProyecto()
    {
        if (!$this->proyecto_id)
            return;

        $proyecto = $this->proyectos->firstWhere('id', $this->proyecto_id);
        if (!$proyecto)
            return;

        // Evitar duplicados
        if (collect($this->proyectos_agregados)->contains('id', $proyecto->id)) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Información',
                'text' => 'El proyecto ya ha sido agregado.'
            ]);
            return;
        }

        $this->proyectos_agregados[] = [
            'id' => $proyecto->id,
            'nombre' => $proyecto->nombre,
            'codigo' => $proyecto->codigo ?? 'N/A'
        ];

        $this->proyecto_id = "";
    }

    public function quitarProyecto($id)
    {
        $this->proyectos_agregados = collect($this->proyectos_agregados)
            ->reject(fn($p) => $p['id'] == $id)
            ->values()
            ->toArray();
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
                'text' => 'Verifique los campos obligatorios y asegurese de agregar al menos un proyecto.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $evento = EntregaFest::create([
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'cliente_id' => $this->cliente_id,
                'user_id' => Auth::id(),
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'codigo' => $this->codigo,
                'fecha_entrega' => $this->fecha_entrega,
                'activo' => $this->activo,
            ]);

            $idsProyectos = collect($this->proyectos_agregados)->pluck('id')->toArray();
            $evento->proyectos()->sync($idsProyectos);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'El evento se ha creado correctamente.'
            ]);

            return redirect()->route('entrega-fest.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error('[ENTREGA-FEST] Error en Creación: ' . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'data' => [
                    'nombre' => $this->nombre,
                    'codigo' => $this->codigo
                ],
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un problema inesperado al guardar el evento. Revise los registros.'
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
