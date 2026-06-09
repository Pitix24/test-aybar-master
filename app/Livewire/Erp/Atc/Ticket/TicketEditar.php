<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Models\EstadoTicket;
use App\Models\TicketHistorial;
use App\Services\ConsultaClienteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;


#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Ticket')]
class TicketEditar extends Component
{
    public Ticket $ticket;

    // Campos editables
    public $email;
    public $celular;
    public $estado_ticket_id;
    public $asunto_respuesta;
    public $descripcion_respuesta;
    public $modalHijosMasivos = false;

    // Gestión de lotes (lista temporal hasta hacer clic en "Actualizar")
    public $lotes_agregados = [];
    public $lote_id = '';
    public Collection $informaciones;
    public bool $lotesBuscados = false;

    // Catálogos y datos para UI
    public $mapEstados = [];

    protected function rules()
    {
        return [
            'email' => 'nullable|email|max:150',
            'celular' => 'nullable|string|max:50',
            'estado_ticket_id' => 'required|exists:estado_tickets,id',
            'asunto_respuesta' => 'nullable|string|max:255',
            'descripcion_respuesta' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->ticket = Ticket::with(['hijos', 'padre.gestor', 'usuariosParticipantes', 'userCliente'])->findOrFail($id);

        $this->email                 = $this->ticket->email;
        $this->celular               = $this->ticket->celular;
        $this->estado_ticket_id      = $this->ticket->estado_ticket_id;
        $this->asunto_respuesta      = $this->ticket->asunto_respuesta;
        $this->descripcion_respuesta = $this->ticket->descripcion_respuesta;
        $this->mapEstados            = EstadoTicket::pluck('nombre', 'id')->toArray();

        // Cargar lotes ya vinculados como lista temporal editable
        $lotes = $this->ticket->lotes;
        if (is_string($lotes)) {
            $lotes = json_decode($lotes, true) ?? [];
        }
        $this->lotes_agregados = is_array($lotes) ? array_values($lotes) : [];

        // Inicializar colección de lotes disponibles del cliente
        $this->informaciones = collect();
    }

    public function validationAttributes()
    {
        return [
            'email' => 'Correo Electrónico',
            'celular' => 'Número de Celular',
            'estado_ticket_id' => 'Estado del Ticket',
            'asunto_respuesta' => 'Asunto de Respuesta',
            'descripcion_respuesta' => 'Descripción de Respuesta',
        ];
    }

    /**
     * Busca los lotes asociados al DNI del cliente del ticket.
     * Replica la lógica de TicketCrear::buscarCliente() para mantener consistencia.
     */
    public function buscarLotesCliente()
    {
        $this->lotesBuscados = true;

        if (empty($this->ticket->dni)) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Sin DNI',
                'text'  => 'El ticket no tiene un DNI/RUC asociado para consultar lotes.',
            ]);
            $this->informaciones = collect();
            return;
        }

        try {
            $consultaService = app(ConsultaClienteService::class);
            $resultado = $consultaService->consultar($this->ticket->dni);

            // 🔧 FIX: el servicio devuelve ['estado', 'mensaje', 'origen', 'data']
            // Los lotes están dentro de la clave 'data'
            $estado = $resultado['estado'] ?? null;
            $lotes  = $resultado['data']   ?? [];

            if ($estado !== 'ok' || empty($lotes)) {
                $mensaje = $resultado['mensaje'] ?? 'No se encontraron lotes vinculados al cliente.';
                $this->informaciones = collect();
                $this->dispatch('alertaLivewire', [
                    'type'  => 'info',
                    'title' => 'Sin resultados',
                    'text'  => $mensaje,
                ]);
                return;
            }

            // Normalizar a la estructura que usa el blade
            $this->informaciones = collect($lotes)->map(function ($item) {
                $arr = is_array($item) ? $item : (array) $item;
                return [
                    'id'           => $arr['id']           ?? null,
                    'razon_social' => $arr['razon_social'] ?? '',
                    'proyecto'     => $arr['proyecto']     ?? '',
                    'numero_lote'  => $arr['numero_lote']  ?? '',
                    // Campos extra útiles para mostrar
                    'codigo_cliente' => $arr['codigo_cliente'] ?? '',
                    'estado_lote'    => $arr['estado_lote']    ?? '',
                    'etapa'          => $arr['etapa']          ?? '',
                ];
            })->filter(fn($l) => !empty($l['id']))->values();

            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => 'Lotes encontrados',
                'text'  => 'Se encontraron ' . $this->informaciones->count() . ' lote(s) vinculado(s) al cliente.',
            ]);

        } catch (\Exception $e) {
            Log::channel('ticket')->error('[TICKET-EDITAR] Error consultando lotes: ' . $e->getMessage(), [
                'ticket_id' => $this->ticket->id,
                'dni'       => $this->ticket->dni,
            ]);
            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => 'Error',
                'text'  => 'No se pudieron consultar los lotes del cliente.',
            ]);
            $this->informaciones = collect();
        }
    }

    /**
     * Agrega un lote a la lista temporal (no persiste hasta Actualizar).
     */
    public function agregarLote()
    {
        if (empty($this->lote_id)) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Selecciona un lote',
                'text'  => 'Debes seleccionar un lote antes de agregarlo.',
            ]);
            return;
        }

        // 🔧 FIX: Buscar usando sintaxis de array
        $lote = collect($this->informaciones)->firstWhere('id', $this->lote_id);

        if (!$lote) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Lote no encontrado',
                'text'  => 'El lote seleccionado no se encuentra en la lista.',
            ]);
            return;
        }

        // Evitar duplicados
        if (collect($this->lotes_agregados)->firstWhere('id', $lote['id'])) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'info',
                'title' => 'Lote ya agregado',
                'text'  => 'Este lote ya forma parte del ticket.',
            ]);
            return;
        }

        $this->lotes_agregados[] = [
            'id'           => $lote['id'],
            'razon_social' => $lote['razon_social'],
            'proyecto'     => $lote['proyecto'],
            'numero_lote'  => $lote['numero_lote'],
        ];

        $this->lote_id = '';

        $this->dispatch('alertaLivewire', [
            'type'  => 'success',
            'title' => 'Lote agregado',
            'text'  => 'Recuerda hacer clic en "Actualizar" para guardar los cambios.',
        ]);
    }

    /**
     * Quita un lote de la lista temporal.
     */
    public function quitarLote($loteId)
    {
        $this->lotes_agregados = array_values(
            array_filter($this->lotes_agregados, fn($l) => $l['id'] != $loteId)
        );
    }

    public function update()
    {
        $this->authorize('ticket.accion-editar');

        // Validar y, si hay errores, notificar pero NO relanzar la excepción
        // para que Livewire pueda pintar los errores por campo en la vista.
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->getMessages();
            // Enviar alerta al frontend
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);

            // Añadir cada error al error bag de Livewire para que se resalten los campos
            foreach ($errors as $field => $messages) {
                $this->addError($field, implode(' | ', $messages));
            }

            // Registrar para diagnóstico en logs de ticket
            Log::channel('ticket')->warning('[TICKET] Validación fallida en edición', [
                'ticket_id' => $this->ticket->id ?? null,
                'usuario_id' => Auth::id(),
                'errors' => $errors,
            ]);

            return; // detener ejecución
        }

        try {
            DB::beginTransaction();

            $old = $this->ticket->fresh();
            $cambios = [];
            $areaTicket = $old->area_id;

            if ($this->estado_ticket_id != $old->estado_ticket_id) {
                $viejo = $this->mapEstados[$old->estado_ticket_id] ?? 'N/A';
                $nuevo = $this->mapEstados[$this->estado_ticket_id] ?? 'N/A';
                $cambios[] = "Estado cambiado de '$viejo' a '$nuevo'";
            }

            if ($this->email != $old->email) {
                $cambios[] = "Correo actualizado de '" . ($old->email ?? 'vacío') . "' a '{$this->email}'";
            }

            if ($this->celular != $old->celular) {
                $cambios[] = "Celular actualizado de '" . ($old->celular ?? 'vacío') . "' a '{$this->celular}'";
            }

            if (trim($this->asunto_respuesta ?? '') !== trim($old->asunto_respuesta ?? '')) {
                $cambios[] = "Asunto respuesta: " . ($this->asunto_respuesta ?? '(vacío)');
            }

            if (trim($this->descripcion_respuesta ?? '') !== trim($old->descripcion_respuesta ?? '')) {
                $cambios[] = "Descripción respuesta: " . ($this->descripcion_respuesta ?? '(vacío)');
            }

            // ===== Detectar cambios en lotes =====
            $lotesAnteriores = is_array($old->lotes)
                ? $old->lotes
                : (json_decode($old->lotes ?? '[]', true) ?: []);

            // Indexar por ID para conservar la info legible
            $mapaAntes = collect($lotesAnteriores)->keyBy('id');
            $mapaAhora = collect($this->lotes_agregados)->keyBy('id');

            $idsAntes = $mapaAntes->keys()->sort()->values()->all();
            $idsAhora = $mapaAhora->keys()->sort()->values()->all();

            if ($idsAntes !== $idsAhora) {
                $idsAgregados  = array_diff($idsAhora, $idsAntes);
                $idsEliminados = array_diff($idsAntes, $idsAhora);

                /**
                 * Helper que agrupa lotes por proyecto y devuelve un string como:
                 *   "ALTOS DEL PRADO A-02, A-03 / VILLA SOL B-15"
                 */
                $formatearAgrupado = function (array $ids, $mapa) {
                    return collect($ids)
                        ->map(fn($id) => $mapa[$id] ?? ['id' => $id])
                        ->groupBy(fn($lote) => $lote['proyecto'] ?? 'N/D')
                        ->map(function ($lotesProyecto, $proyecto) {
                            $mzLts = collect($lotesProyecto)
                                ->pluck('numero_lote')
                                ->filter()
                                ->implode(', ');
                            return trim("{$proyecto} {$mzLts}");
                        })
                        ->implode(' / ');
                };

                $detalle = [];

                if (!empty($idsAgregados)) {
                    $detalle[] = 'Lotes agregados: ' . $formatearAgrupado($idsAgregados, $mapaAhora);
                }

                if (!empty($idsEliminados)) {
                    $detalle[] = 'Lotes eliminados: ' . $formatearAgrupado($idsEliminados, $mapaAntes);
                }

                $cambios[] = implode(' | ', $detalle);
            }

            $this->ticket->update([
                'area_id'                => $areaTicket,
                'email'                  => $this->email,
                'celular'                => $this->celular,
                'estado_ticket_id'       => $this->estado_ticket_id,
                'asunto_respuesta'       => $this->asunto_respuesta,
                'descripcion_respuesta'  => $this->descripcion_respuesta,
                'lotes'                  => $this->lotes_agregados,
                'updated_by'             => Auth::id(),
            ]);


            // Registrar al usuario que edita como participante
            $this->ticket->usuariosParticipantes()->syncWithoutDetaching([Auth::id()]);

            if (!empty($cambios)) {
                TicketHistorial::create([
                    'ticket_id' => $this->ticket->id,
                    'user_id' => Auth::id(),
                    'accion' => 'Edición',
                    'detalle' => implode(" | ", $cambios),
                ]);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'Cambios guardados correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error en Edición: ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'ticket_id' => $this->ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudieron guardar los cambios. Intente nuevamente.'
            ]);
        }
    }

    #[On('eliminarTicketOn')]
    public function eliminarTicketOn()
    {
        $this->authorize('ticket.accion-eliminar');

        try {
            if ($this->ticket->hijos()->exists()) {
                $this->dispatch('alertaLivewire', [
                    'type' => 'warning',
                    'title' => 'No permitido',
                    'text' => 'Este ticket tiene tickets hijos asociados y no puede ser eliminado.'
                ]);
                return;
            }

            $ticket_id = $this->ticket->id;
            $this->ticket->delete();

            return redirect()->route('erp.ticket.vista.todo');
        } catch (\Exception $e) {
            Log::channel('ticket')->error('[TICKET] Error en Eliminación: ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'ticket_id' => $this->ticket->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el ticket.'
            ]);
        }
    }

    public function abrirHijosMasivos()
    {
        $this->modalHijosMasivos = true;
    }

    #[On('cerrarModalHijosMasivos')]
    public function cerrarHijosMasivos()
    {
        $this->modalHijosMasivos = false;
    }

    #[On('refreshHijos')]
    public function refreshHijos()
    {
        $this->ticket->load('hijos');
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-editar', [
            'estados' => EstadoTicket::where('activo', true)->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
