<?php

namespace App\Livewire\Erp\Cita\Cita;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\MotivoCita;
use App\Models\Sede;
use App\Models\Area;
use App\Models\Ticket;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class CitaEditar extends Component
{
    public Cita $cita;
    public $ticket;

    // Campos del formulario
    public $gestor_id = '';
    public $area_id = '';
    public $sede_id = '';
    public $motivo_cita_id = '';
    public $estado_cita_id = '';

    public $fecha;          // YYYY-MM-DD
    public $hora_inicio;    // HH:mm
    public $hora_fin;

    public $asunto_solicitud;
    public $descripcion_solicitud;

    // Campos de respuesta / atención (específicos de edición/seguimiento)
    public $asunto_respuesta;
    public $descripcion_respuesta;

    // Datos Cliente (Referencia)
    public $dni;
    public $nombres;
    public $origen;

    protected $duracionMinutos = 60;

    protected function rules()
    {
        return [
            'area_id' => 'required|exists:areas,id',
            'gestor_id' => 'required|exists:users,id',
            'sede_id' => 'required|exists:sedes,id',
            'motivo_cita_id' => 'required|exists:motivo_citas,id',
            'estado_cita_id' => 'required|exists:estado_citas,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'asunto_solicitud' => 'required|string|max:255',
            'descripcion_solicitud' => 'required|string',
            'asunto_respuesta' => 'nullable|string|max:255',
            'descripcion_respuesta' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->cita = Cita::with(['ticket', 'area', 'gestor'])->findOrFail($id);
        $this->ticket = $this->cita->ticket;

        $this->gestor_id = $this->cita->gestor_id;
        $this->area_id = $this->cita->area_id;
        $this->sede_id = $this->cita->sede_id;
        $this->motivo_cita_id = $this->cita->motivo_cita_id;
        $this->estado_cita_id = $this->cita->estado_cita_id;

        $this->asunto_solicitud = $this->cita->asunto_solicitud;
        $this->descripcion_solicitud = $this->cita->descripcion_solicitud;
        $this->asunto_respuesta = $this->cita->asunto_respuesta;
        $this->descripcion_respuesta = $this->cita->descripcion_respuesta;

        $this->dni = $this->cita->dni;
        $this->nombres = $this->cita->nombres;
        $this->origen = $this->cita->origen;

        if ($this->cita->fecha_inicio) {
            $this->fecha = $this->cita->fecha_inicio->format('Y-m-d');
            $this->hora_inicio = $this->cita->fecha_inicio->format('H:i');
        }

        if ($this->cita->fecha_fin) {
            $this->hora_fin = $this->cita->fecha_fin->format('H:i');
        } else {
            $this->updatedHoraInicio($this->hora_inicio);
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

        $gestoresDisp = $area->users()
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();

        if (!$gestoresDisp->contains('id', $this->gestor_id)) {
            $principal = $gestoresDisp->first(fn($u) => (bool) $u->pivot->is_principal);
            $this->gestor_id = $principal ? $principal->id : $gestoresDisp->first()?->id;
        }
    }

    public function updatedHoraInicio($value)
    {
        if (!$this->fecha || !$value || strpos($value, ':') === false)
            return;

        try {
            $this->hora_fin = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$value}")
                ->addMinutes($this->duracionMinutos)
                ->format('H:i');
        } catch (\Exception $e) {
            // Silencioso si el formato falla durante el tipeo
        }
    }

    public function update()
    {
        abort_unless(auth()->user()->can('cita.editar'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Faltan campos obligatorios.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $fechaInicio = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->hora_inicio}");
            $fechaFin = Carbon::createFromFormat('Y-m-d H:i', "{$this->fecha} {$this->hora_fin}");

            $this->cita->update([
                'area_id' => $this->area_id,
                'gestor_id' => $this->gestor_id,
                'sede_id' => $this->sede_id,
                'motivo_cita_id' => $this->motivo_cita_id,
                'estado_cita_id' => $this->estado_cita_id,

                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,

                'asunto_solicitud' => $this->asunto_solicitud,
                'descripcion_solicitud' => $this->descripcion_solicitud,
                'asunto_respuesta' => $this->asunto_respuesta,
                'descripcion_respuesta' => $this->descripcion_respuesta,

                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'La cita ha sido actualizada correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al actualizar la cita.']);
            return;
        }
    }

    #[On('eliminarCitaOn')]
    public function eliminarCitaOn()
    {
        abort_unless(auth()->user()->can('cita.eliminar'), 403);

        try {
            DB::beginTransaction();
            $this->cita->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'La cita ha sido eliminada correctamente.']);
            return redirect()->route('erp.cita.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
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

        return view('livewire.erp.cita.cita.cita-editar', compact('areas', 'sedes', 'motivos', 'estados', 'gestores'));
    }
}
