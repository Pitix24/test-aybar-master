<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\User;
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
    public $unidad_negocio_id, $gestor_id;
    public $proyecto_id = ""; // Para el select
    public $proyectos_agregados = []; // Para la tabla
    public $activo;

    // Catálogos
    public $unidades_negocios = [];
    public $proyectos = [];
    public $gestores = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo' => 'required|string|max:50|unique:entrega_fests,codigo,' . $this->evento->id,
            'fecha_entrega' => 'required|date',
            'proyectos_agregados' => 'required|array|min:1',
            'proyectos_agregados.*.id' => 'exists:proyectos,id',
            'gestor_id' => 'nullable|exists:users,id',
            'activo' => 'boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'unidad_negocio_id' => 'Unidad de Negocio',
            'proyectos_agregados' => 'Proyectos del Evento',
            'gestor_id' => 'Gestor Responsable',
            'fecha_entrega' => 'Fecha de Entrega',
            'codigo' => 'Código Único',
            'nombre' => 'Nombre del Evento'
        ];
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['proyectos.unidadNegocio', 'gestor'])->findOrFail($id);

        // Carga de datos iniciales
        $this->nombre = $this->evento->nombre;
        $this->descripcion = $this->evento->descripcion;
        $this->codigo = $this->evento->codigo;
        $this->fecha_entrega = $this->evento->fecha_entrega->format('Y-m-d');
        $this->gestor_id = $this->evento->gestor_id;
        $this->activo = $this->evento->activo;

        // Carga de proyectos vinculados
        foreach ($this->evento->proyectos as $p) {
            $this->proyectos_agregados[] = [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'unidad_negocio_nombre' => $p->unidadNegocio->nombre ?? 'N/A',
                'codigo' => $p->codigo ?? 'N/A'
            ];
        }

        // Carga de catálogos
        $this->unidades_negocios = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $this->gestores = User::role(['asesor-backoffice', 'supervisor-backoffice', 'super-admin'])->get();

        if (empty($this->gestores)) {
            $this->gestores = User::where('activo', true)->orderBy('name')->limit(100)->get();
        }
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = "";
        $this->proyectos = [];
        if ($value) {
            $this->loadProyectos();
        }
    }

    public function loadProyectos()
    {
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::with('unidadNegocio')
                ->where('unidad_negocio_id', $this->unidad_negocio_id)
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
            'unidad_negocio_nombre' => $proyecto->unidadNegocio->nombre ?? 'N/A',
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
                'gestor_id' => $this->gestor_id,
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'codigo' => $this->codigo,
                'fecha_entrega' => $this->fecha_entrega,
                'activo' => $this->activo,
            ]);

            $idsProyectos = collect($this->proyectos_agregados)->pluck('id')->toArray();
            $this->evento->proyectos()->sync($idsProyectos);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Los cambios se han guardado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error('[ENTREGA-FEST] Error en Edición: ' . $e->getMessage(), [
                'evento_id' => $this->evento->id,
                'usuario_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el evento: ' . $e->getMessage()
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
