<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Entrega Fest')]
class EntregaFestEditar extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;

    // Campos del Evento
    public $nombre, $descripcion, $codigo, $fecha_entrega, $gestor_id;
    public $unidad_negocio_id = ""; // Para el select
    public $proyecto_id = ""; // Para el select
    public $proyectos_agregados = []; // Para la tabla
    public $activo;

    // Catálogos
    public $unidades_negocios = [];
    public $proyectos = [];
    public $gestores = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos.unidadNegocio')->findOrFail($id);

        $this->nombre = $this->evento->nombre;
        $this->descripcion = $this->evento->descripcion;
        $this->codigo = $this->evento->codigo;
        $this->fecha_entrega = $this->evento->fecha_entrega ? $this->evento->fecha_entrega->format('Y-m-d') : null;
        $this->gestor_id = $this->evento->gestor_id;
        $this->activo = $this->evento->activo;

        $this->proyectos_agregados = $this->evento->proyectos->map(fn($p) => [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'unidad_negocio_nombre' => $p->unidadNegocio->nombre ?? 'N/A',
            'codigo' => $p->codigo ?? 'N/A'
        ])->toArray();

        $this->unidades_negocios = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $this->gestores = User::permission('entrega-fest.gestor')->get();
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = "";
        if ($value) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $value)->where('activo', true)->orderBy('nombre')->get();
        } else {
            $this->proyectos = [];
        }
    }

    public function agregarProyecto()
    {
        if (!$this->proyecto_id)
            return;

        $proyecto = Proyecto::with('unidadNegocio')->find($this->proyecto_id);

        if (collect($this->proyectos_agregados)->contains('id', $proyecto->id)) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'El proyecto ya ha sido agregado.']);
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
        $this->proyectos_agregados = collect($this->proyectos_agregados)->reject(fn($p) => $p['id'] == $id)->values()->toArray();
    }

    public function update()
    {
        $this->authorize('entrega-fest.editar');

        $this->validate([
            'nombre' => 'required|string|max:255',
            'gestor_id' => 'required',
            'fecha_entrega' => 'required|date',
        ]);

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
                'text' => 'Los cambios generales se han guardado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error('[ENTREGA-FEST] Error en Edición: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-editar');
    }
}
