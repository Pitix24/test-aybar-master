<?php

namespace App\Livewire\Cita\Cita;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\MotivoCita;
use App\Models\Sede;
use App\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
class CitaEditar extends Component
{
    public Cita $cita;

    // Campos editables
    public $gestor_id;
    public $estado_cita_id;
    public $asunto_respuesta;
    public $descripcion_respuesta;
    public $area_id;
    public $sede_id;
    public $motivo_cita_id;

    public $fecha;
    public $hora_inicio;
    public $hora_fin;

    protected function rules()
    {
        return [
            'gestor_id' => 'required|exists:users,id',
            'estado_cita_id' => 'required|exists:estado_citas,id',
            'area_id' => 'required|exists:areas,id',
            'sede_id' => 'required|exists:sedes,id',
            'motivo_cita_id' => 'required|exists:motivo_citas,id',
            'asunto_respuesta' => 'nullable|string|max:255',
            'descripcion_respuesta' => 'nullable|string',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ];
    }

    public function mount($id)
    {
        $this->cita = Cita::with(['ticket', 'area', 'gestor'])->findOrFail($id);

        $this->gestor_id = $this->cita->gestor_id;
        $this->estado_cita_id = $this->cita->estado_cita_id;
        $this->asunto_respuesta = $this->cita->asunto_respuesta;
        $this->descripcion_respuesta = $this->cita->descripcion_respuesta;
        $this->area_id = $this->cita->area_id;
        $this->sede_id = $this->cita->sede_id;
        $this->motivo_cita_id = $this->cita->motivo_cita_id;

        if ($this->cita->fecha_inicio) {
            $this->fecha = $this->cita->fecha_inicio->format('Y-m-d');
            $this->hora_inicio = $this->cita->fecha_inicio->format('H:i');
        }

        if ($this->cita->fecha_fin) {
            $this->hora_fin = $this->cita->fecha_fin->format('H:i');
        }
    }

    public function updatedAreaId($value)
    {
        $this->cargarGestores($value);
    }

    public function cargarGestores($areaId)
    {
        $area = Area::find($areaId);
        if (!$area) {
            $this->gestor_id = '';
            return;
        }

        $gestoresDisp = $area->users()->where('activo', true)->get();
        if (!$gestoresDisp->contains('id', $this->gestor_id)) {
            $this->gestor_id = $gestoresDisp->first()?->id;
        }
    }

    public function update()
    {
        abort_unless(auth()->user()->can('cita.editar'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores en el formulario.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $fechaInicio = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->hora_inicio}");
            $fechaFin = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->hora_fin}");

            $this->cita->update([
                'gestor_id' => $this->gestor_id,
                'estado_cita_id' => $this->estado_cita_id,
                'area_id' => $this->area_id,
                'sede_id' => $this->sede_id,
                'motivo_cita_id' => $this->motivo_cita_id,
                'asunto_respuesta' => $this->asunto_respuesta,
                'descripcion_respuesta' => $this->descripcion_respuesta,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'La cita ha sido actualizada correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al actualizar la cita.']);
        }
    }

    #[On('eliminarCitaOn')]
    public function eliminarCitaOn()
    {
        abort_unless(auth()->user()->can('cita.eliminar'), 403);

        try {
            $this->cita->delete();
            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'La cita ha sido eliminada.']);
            return redirect()->route('erp.cita.vista.todo');
        } catch (\Exception $e) {
            Log::error('Error al eliminar cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar la cita.']);
        }
    }

    public function render()
    {
        $areas = Area::where('activo', true)->orderBy('nombre')->get();
        $sedes = Sede::where('activo', true)->orderBy('nombre')->get();
        $motivos = MotivoCita::where('activo', true)->orderBy('nombre')->get();
        $estados = EstadoCita::where('activo', true)->get();

        $areaSel = Area::find($this->area_id);
        $gestores = $areaSel ? $areaSel->users()->where('activo', true)->orderBy('users.name')->get() : collect();

        return view('livewire.cita.cita.cita-editar', compact('areas', 'sedes', 'motivos', 'estados', 'gestores'));
    }
}
