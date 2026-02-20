<?php

namespace App\Livewire\Erp\Cita\Cita;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\MotivoCita;
use App\Models\Sede;
use App\Models\Ticket;
use App\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class CitaCrear extends Component
{
    public ?int $citaPadreId = null; // En rutas se definió como citaPadre? pero aybarcorp usa ticketId
    public $ticket;

    // Si viene de un ticket
    public $ticket_id;

    // Campos del formulario
    public $gestor_id = '';
    public $area_id = '';
    public $sede_id = '';
    public $motivo_cita_id = '';
    public $estado_cita_id = 1; // Por defecto el primero que suela ser 'Pendiente' o 'Programada'

    public $fecha;          // YYYY-MM-DD
    public $hora_inicio;    // HH:mm
    public $hora_fin;

    public $asunto_solicitud;
    public $descripcion_solicitud;

    // Datos Cliente (Autocompletados si hay ticket)
    public $dni;
    public $nombres;
    public $origen;
    public $cliente_id;
    public $unidad_negocio_id;
    public $proyecto_id;

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
        ];
    }

    public function mount($citaPadre = null) // citaPadre es el ID del TICKET según las rutas de aybarcorp/aybar
    {
        if ($citaPadre) {
            $this->ticket_id = $citaPadre;
            $this->ticket = Ticket::find($citaPadre);

            if ($this->ticket) {
                $this->dni = $this->ticket->dni;
                $this->nombres = $this->ticket->nombres;
                $this->origen = $this->ticket->origen;
                $this->cliente_id = $this->ticket->cliente_id;
                $this->unidad_negocio_id = $this->ticket->unidad_negocio_id;
                $this->proyecto_id = $this->ticket->proyecto_id;
                $this->area_id = $this->ticket->area_id;
                $this->asunto_solicitud = "Cita: " . $this->ticket->asunto_inicial;

                if ($this->area_id) {
                    $this->cargarGestores($this->area_id);
                }
            }
        }

        $this->fecha = date('Y-m-d');
        $this->hora_inicio = date('H:i');
        $this->updatedHoraInicio($this->hora_inicio);

        // Cargar estado inicial (ej: Programada)
        $this->estado_cita_id = EstadoCita::where('activo', true)->orderBy('id')->value('id');
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

        $user = auth()->user();
        if ($gestoresDisp->contains('id', $user->id)) {
            $this->gestor_id = $user->id;
        } else {
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

    public function store()
    {
        abort_unless(auth()->user()->can('cita.crear'), 403);

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

            $cita = Cita::create([
                'ticket_id' => $this->ticket_id,
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'cliente_id' => $this->cliente_id,
                'area_id' => $this->area_id,

                'usuario_crea_id' => auth()->id(),
                'gestor_id' => $this->gestor_id,
                'sede_id' => $this->sede_id,
                'motivo_cita_id' => $this->motivo_cita_id,
                'estado_cita_id' => $this->estado_cita_id,

                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,

                'asunto_solicitud' => $this->asunto_solicitud,
                'descripcion_solicitud' => $this->descripcion_solicitud,

                'dni' => $this->dni,
                'nombres' => $this->nombres,
                'origen' => $this->origen,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'La cita ha sido programada correctamente.']);

            return redirect()->route('erp.cita.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al guardar la cita.']);
            return;
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

        return view('livewire.erp.cita.cita.cita-crear', compact('areas', 'sedes', 'motivos', 'estados', 'gestores'));
    }
}
