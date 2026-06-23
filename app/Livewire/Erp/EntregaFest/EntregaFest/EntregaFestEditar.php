<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEnvioEntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use App\Models\User;
use App\Models\Erp\EntregaFest\EntregaFestHistorialComunicacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    public function cancelarEvento()
    {
        // Actualizamos en BD
        $this->evento->update(['activo' => false]);
        $this->evento->prospectos()->update(['activo' => false]);

        // CLAVE: Actualizamos la propiedad pública para que la vista cambie sin recargar
        $this->activo = false;

        // Disparamos alerta de éxito (Ajusta a tu método de alertas)
        $this->dispatch('alerta', [
            'tipo' => 'success',
            'mensaje' => 'Evento y prospectos cancelados correctamente.'
        ]);
    }

    #[On('eliminarEntregaFestOn')]
    public function eliminarEntregaFestOn()
    {
        $this->authorize('entrega-fest.eliminar');

        try {
            DB::beginTransaction();

            $evento = $this->evento->load(['proyectos', 'prospectos.copropietarios']);

            foreach ($evento->prospectos as $prospecto) {
                $this->eliminarColeccion($prospecto->historialComunicaciones);
                $this->eliminarColeccion($prospecto->bancarizaciones);
                $this->eliminarColeccion($prospecto->acompanantes);

                foreach ($prospecto->copropietarios as $copropietario) {
                    $this->eliminarColeccion($copropietario->historialComunicaciones);

                    if ($copropietario->invitado) {
                        $this->eliminarInvitado($copropietario->invitado);
                    }

                    $this->eliminarModelo($copropietario);
                }

                if ($prospecto->invitado) {
                    $this->eliminarInvitado($prospecto->invitado);
                }

                $this->eliminarModelo($prospecto);
            }

            $this->eliminarColeccion(EntregaFestHistorialComunicacion::where('entrega_fest_id', $evento->id)->get());

            foreach ($evento->itinerarioBloques as $bloque) {
                $this->eliminarModelo($bloque);
            }

            foreach ($evento->mopTareas as $tarea) {
                $this->eliminarModelo($tarea);
            }

            foreach ($evento->proveedores as $proveedor) {
                $this->eliminarColeccion($proveedor->requerimientos);
                $this->eliminarModelo($proveedor);
            }

            foreach ($evento->incidencias as $incidencia) {
                $this->eliminarModelo($incidencia);
            }

            foreach ($evento->recursos as $recurso) {
                $this->eliminarModelo($recurso);
            }

            foreach ($evento->protocolos as $protocolo) {
                $this->eliminarModelo($protocolo);
            }

            foreach ($evento->contingencias as $contingencia) {
                $this->eliminarModelo($contingencia);
            }

            foreach ($evento->plantillas as $plantilla) {
                $this->eliminarModelo($plantilla);
            }

            $evento->proyectos()->detach();
            $evento->forceDelete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'El evento y sus relaciones principales se eliminaron correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('entrega-fest')->error('[ENTREGA-FEST] Error al eliminar: ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'evento_id' => $this->evento->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el evento.'
            ]);
        }
    }

    protected function eliminarInvitado($invitado): void
    {
        $this->eliminarColeccion($invitado->acompanantes);
        $this->eliminarColeccion(InvitadoEnvioEntregaFest::where('invitado_entrega_fest_id', $invitado->id)->get());
        $this->eliminarModelo($invitado->asistencia);
        $this->eliminarModelo($invitado);
    }

    protected function eliminarColeccion($registros): void
    {
        foreach ($registros as $registro) {
            $this->eliminarModelo($registro);
        }
    }

    protected function eliminarModelo(?Model $registro): void
    {
        if (!$registro) {
            return;
        }

        if (in_array(SoftDeletes::class, class_uses_recursive($registro))) {
            $registro->forceDelete();

            return;
        }

        $registro->delete();
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-editar');
    }
}
