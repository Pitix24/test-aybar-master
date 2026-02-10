<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Ticket;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Area;
use App\Models\TipoSolicitud;
use App\Models\SubTipoSolicitud;
use App\Models\Canal;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use App\Models\TicketArchivo;
use App\Models\TicketHistorial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\WithFileUploads;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class TicketEditar extends Component
{
    use WithFileUploads;

    public Ticket $ticket;

    // Datos del Ticket (Vista General)
    public $unidad_negocio_id;
    public $proyecto_id;
    public $cliente_id;
    public $area_id;
    public $tipo_solicitud_id;
    public $sub_tipo_solicitud_id;
    public $canal_id;
    public $estado_ticket_id;
    public $prioridad_ticket_id;
    public $gestor_id;
    public $asunto_inicial;
    public $descripcion_inicial;

    // Datos Cliente
    public $dni;
    public $nombres;
    public $email;
    public $celular;
    public $origen;
    public $lotes = [];

    // Respuesta
    public $asunto_respuesta;
    public $descripcion_respuesta;

    // Datos Participantes
    public $searchUser = '';
    public $selectedParticipants = [];

    // Adjuntos
    public $archivo;
    public $descripcion_archivo;
    public $archivos_existentes;
    public $tab_activa = 'ticket'; // ticket, historial, adjuntos

    // UI
    public $activeTab = 'general'; // general, cliente

    protected function rules()
    {
        return [
            'asunto_inicial' => 'required|min:5|max:255',
            'descripcion_inicial' => 'required|min:10',
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'cliente_id' => 'nullable',
            'area_id' => 'required|exists:areas,id',
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',
            'sub_tipo_solicitud_id' => 'nullable|exists:sub_tipo_solicituds,id',
            'canal_id' => 'required|exists:canals,id',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
            'prioridad_ticket_id' => 'required|exists:prioridad_tickets,id',
            'gestor_id' => 'nullable|exists:users,id',
            'asunto_respuesta' => 'nullable|string',
            'descripcion_respuesta' => 'nullable|string',
            'email' => 'nullable|email',
            'celular' => 'nullable|string',
            'selectedParticipants' => 'nullable|array',
            'selectedParticipants.*' => 'exists:users,id',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::with(['hijos', 'usuariosParticipantes'])->findOrFail($id);

        $this->unidad_negocio_id = $this->ticket->unidad_negocio_id;
        $this->proyecto_id = $this->ticket->proyecto_id;
        $this->cliente_id = $this->ticket->cliente_id;
        $this->area_id = $this->ticket->area_id;
        $this->tipo_solicitud_id = $this->ticket->tipo_solicitud_id;
        $this->sub_tipo_solicitud_id = $this->ticket->sub_tipo_solicitud_id;
        $this->canal_id = $this->ticket->canal_id;
        $this->estado_ticket_id = $this->ticket->estado_ticket_id;
        $this->prioridad_ticket_id = $this->ticket->prioridad_ticket_id;
        $this->gestor_id = $this->ticket->gestor_id;
        $this->asunto_inicial = $this->ticket->asunto_inicial;
        $this->descripcion_inicial = $this->ticket->descripcion_inicial;

        $this->dni = $this->ticket->dni;
        $this->nombres = $this->ticket->nombres;
        $this->email = $this->ticket->email;
        $this->celular = $this->ticket->celular;
        $this->origen = $this->ticket->origen;
        $this->lotes = $this->ticket->lotes ?? [];

        $this->asunto_respuesta = $this->ticket->asunto_respuesta;
        $this->descripcion_respuesta = $this->ticket->descripcion_respuesta;

        $this->selectedParticipants = $this->ticket->usuariosParticipantes()->pluck('users.id')->toArray();
        $this->archivos_existentes = $this->ticket->archivos()->latest()->get();
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
    }

    public function updatedTipoSolicitudId($value)
    {
        $this->sub_tipo_solicitud_id = '';
    }

    public function updatedAreaId($value)
    {
        $this->cargarDatosArea($value);
    }

    public function cargarDatosArea($areaId)
    {
        $area = Area::find($areaId);
        if (!$area)
            return;

        $gestoresDisp = $area->users()
            ->where('activo', true)
            ->withPivot('is_principal')
            ->orderByDesc('area_user.is_principal')
            ->orderBy('users.name')
            ->get();

        $principal = $gestoresDisp->first(fn($u) => (bool) $u->pivot->is_principal);
        $this->gestor_id = $principal ? $principal->id : $gestoresDisp->first()?->id;

        $this->tipo_solicitud_id = '';
        $this->sub_tipo_solicitud_id = '';
    }

    public function addParticipant($userId)
    {
        if (!in_array($userId, $this->selectedParticipants)) {
            $this->selectedParticipants[] = $userId;
        }
        $this->searchUser = '';
    }

    public function removeParticipant($userId)
    {
        $this->selectedParticipants = array_diff($this->selectedParticipants, [$userId]);
    }

    public function update()
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores en el formulario.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $old = $this->ticket->toArray();

            $data = [
                'asunto_inicial' => $this->asunto_inicial,
                'descripcion_inicial' => $this->descripcion_inicial,
                'unidad_negocio_id' => $this->unidad_negocio_id,
                'proyecto_id' => $this->proyecto_id,
                'area_id' => $this->area_id,
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'sub_tipo_solicitud_id' => $this->sub_tipo_solicitud_id ?: null,
                'canal_id' => $this->canal_id,
                'estado_ticket_id' => $this->estado_ticket_id,
                'prioridad_ticket_id' => $this->prioridad_ticket_id,
                'gestor_id' => $this->gestor_id ?: null,
                'asunto_respuesta' => $this->asunto_respuesta,
                'descripcion_respuesta' => $this->descripcion_respuesta,
                'email' => $this->email,
                'celular' => $this->celular,
                'updated_by' => auth()->id(),
            ];

            $this->ticket->update($data);

            // Sincronizar participantes
            $this->ticket->usuariosParticipantes()->sync($this->selectedParticipants);

            // Generar Historial de cambios
            $cambios = [];
            foreach ($data as $campo => $valorNuevo) {
                if ($campo === 'updated_by')
                    continue;

                $valorViejo = $old[$campo] ?? null;
                if ($valorNuevo != $valorViejo) {
                    $nombreCampo = $this->nombreCampo($campo);
                    $viejo = $this->valorLegible($campo, $valorViejo);
                    $nuevo = $this->valorLegible($campo, $valorNuevo);
                    $cambios[] = "$nombreCampo cambiado de '$viejo' a '$nuevo'";
                }
            }

            if (!empty($cambios)) {
                TicketHistorial::create([
                    'ticket_id' => $this->ticket->id,
                    'user_id' => auth()->id(),
                    'accion' => 'Edición',
                    'detalle' => implode(" | ", $cambios),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'El ticket ha sido actualizado correctamente.']);
            $this->archivos_existentes = $this->ticket->archivos()->latest()->get(); // Refrescar por si acaso
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al actualizar el ticket.']);
            return;
        }
    }

    protected function nombreCampo($campo)
    {
        return match ($campo) {
            'asunto_inicial' => 'Asunto Inicial',
            'descripcion_inicial' => 'Descripción Inicial',
            'unidad_negocio_id' => 'Unidad de Negocio',
            'proyecto_id' => 'Proyecto',
            'area_id' => 'Área',
            'tipo_solicitud_id' => 'Tipo de Solicitud',
            'sub_tipo_solicitud_id' => 'Subtipo de Solicitud',
            'canal_id' => 'Canal',
            'estado_ticket_id' => 'Estado',
            'prioridad_ticket_id' => 'Prioridad',
            'gestor_id' => 'Gestor',
            'asunto_respuesta' => 'Asunto Respuesta',
            'descripcion_respuesta' => 'Descripción Respuesta',
            default => ucfirst(str_replace('_', ' ', $campo))
        };
    }

    protected function valorLegible($campo, $valor)
    {
        if ($valor === null || $valor === '')
            return 'N/A';

        return match ($campo) {
            'unidad_negocio_id' => UnidadNegocio::find($valor)?->nombre ?? $valor,
            'proyecto_id' => Proyecto::find($valor)?->nombre ?? $valor,
            'area_id' => Area::find($valor)?->nombre ?? $valor,
            'tipo_solicitud_id' => TipoSolicitud::find($valor)?->nombre ?? $valor,
            'sub_tipo_solicitud_id' => SubTipoSolicitud::find($valor)?->nombre ?? $valor,
            'canal_id' => Canal::find($valor)?->nombre ?? $valor,
            'estado_ticket_id' => EstadoTicket::find($valor)?->nombre ?? $valor,
            'prioridad_ticket_id' => PrioridadTicket::find($valor)?->nombre ?? $valor,
            'gestor_id' => User::find($valor)?->name ?? $valor,
            default => $valor
        };
    }

    public function adjuntar()
    {
        abort_unless(auth()->user()->can('ticket.editar'), 403);

        try {
            $this->validate([
                'archivo' => 'required|max:51200', // 50MB max
                'descripcion_archivo' => 'required|min:3',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Especifique el archivo y una descripción clara.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $filename = $this->archivo->getClientOriginalName();
            $extension = $this->archivo->getClientOriginalExtension();
            $path = $this->archivo->store('tickets/' . $this->ticket->id, 'public');

            TicketArchivo::create([
                'archivable_type' => Ticket::class,
                'archivable_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'nombre_original' => $filename,
                'path' => $path,
                'url' => Storage::url($path),
                'descripcion' => $this->descripcion_archivo,
                'extension' => $extension,
                'size' => $this->archivo->getSize(),
                'mime_type' => $this->archivo->getMimeType(),
            ]);

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Adjuntar archivo',
                'detalle' => "Se adjuntó el archivo '{$this->descripcion_archivo}' ({$filename})",
            ]);

            DB::commit();

            $this->reset(['archivo', 'descripcion_archivo']);
            $this->archivos_existentes = $this->ticket->archivos()->latest()->get();

            $this->dispatch('alertaLivewire', ['title' => 'Adjuntado', 'text' => 'El archivo se ha subido correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al adjuntar archivo: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo subir el archivo.']);
        }
    }

    public function cancelarAdjunto()
    {
        $this->reset(['archivo', 'descripcion_archivo']);
    }

    #[On('eliminarArchivoOn')]
    public function eliminarArchivo($archivoId)
    {
        abort_unless(auth()->user()->can('ticket.eliminar'), 403);

        try {
            $archivo = TicketArchivo::findOrFail($archivoId);

            if (Storage::disk('public')->exists($archivo->path)) {
                Storage::disk('public')->delete($archivo->path);
            }

            $desc = $archivo->descripcion;
            $name = $archivo->nombre_original;
            $archivo->delete();

            TicketHistorial::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'accion' => 'Eliminar archivo',
                'detalle' => "Se eliminó el archivo '{$desc}' ({$name})",
            ]);

            $this->archivos_existentes = $this->ticket->archivos()->latest()->get();
            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El archivo ha sido eliminado.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al eliminar el archivo.']);
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        abort_unless(auth()->user()->can('ticket.eliminar'), 403);

        try {
            $ticket = $this->ticket->fresh();

            if ($ticket->estado_ticket_id != 1 && !auth()->user()->hasRole('admin')) {
                $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Solo se pueden eliminar tickets en estado ABIERTO.']);
                return;
            }

            if ($ticket->hijos()->exists()) {
                $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Este ticket tiene tickets hijos asociados. No se puede eliminar.']);
                return;
            }

            DB::beginTransaction();
            $ticket->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'El ticket ha sido eliminado.']);
            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar ticket: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar el ticket.']);
        }
    }

    public function render()
    {
        $unidades = UnidadNegocio::where('activo', true)->orderBy('nombre')->get();
        $proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->where('activo', true)->orderBy('nombre')->get();
        $areas = Area::where('activo', true)->orderBy('nombre')->get();

        $areaSel = Area::find($this->area_id);
        $tipos = $areaSel ? $areaSel->tiposSolicitud()->where('activo', true)->get() : collect();
        $subtipos = SubTipoSolicitud::where('tipo_solicitud_id', $this->tipo_solicitud_id)->where('activo', true)->orderBy('nombre')->get();

        $canales = Canal::where('activo', true)->orderBy('nombre')->get();
        $estados = EstadoTicket::where('activo', true)->get();
        $prioridades = PrioridadTicket::where('activo', true)->get();

        $gestores = $areaSel ? $areaSel->users()->where('activo', true)->orderBy('users.name')->get() : collect();

        $participantesDisponibles = [];
        if (strlen($this->searchUser) > 2) {
            $participantesDisponibles = User::where('activo', true)
                ->where('name', 'like', "%{$this->searchUser}%")
                ->whereNotIn('id', $this->selectedParticipants)
                ->limit(5)
                ->get();
        }

        $participantesSeleccionados = User::whereIn('id', $this->selectedParticipants)->get();
        $historialFull = $this->ticket->historial()->with('user')->latest()->get();

        return view('livewire.atc.ticket.ticket-editar', compact(
            'unidades',
            'proyectos',
            'areas',
            'tipos',
            'subtipos',
            'canales',
            'estados',
            'prioridades',
            'gestores',
            'participantesDisponibles',
            'participantesSeleccionados',
            'historialFull'
        ));
    }
}
