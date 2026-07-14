<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Events\EntregaFest\EntregaFestPreInvitacion;
use App\Events\EntregaFest\EntregaFestAsistenciaInvitacionMasivo;
use App\Exports\EntregaFest\EntregaFestProspectoExport;
use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\{Layout, Lazy, Title, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Prospectos del Evento')]
class EntregaFestProspecto extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    /** Lista centralizada de propiedades que son filtros. */
    protected array $propiedadesFiltro = [
        'buscar',
        'proyecto_id',
        'filtro_manzana',
        'filtro_activo',
        'filtro_observacion_legal',
        'filtro_observacion_legal',
        'con_historico',
        'filtro_lote_entregado',
        'filtroGestorBackoffice',
        'estado_backoffice',
        'estado_gestor_backoffice',
        'estado_contrato_preeliminar_emitido',
        'estado_firma_contrato_firmado',
        'grupo',
        'filtro_confirmacion',
        'filtro_invitacion',
        'gestor_id',
        'estado_cliente_id',
        'gestor_legal_id',
        'fechaFirmaDesde',
        'fechaFirmaHasta',
        'fechaGeneracionDesde',
        'fechaGeneracionHasta',
        'perPage',
    ];

    public bool $modoAsignacionMasiva = false;
    public array $selectedProspectos = [];
    public bool $selectAll = false;

    // NUEVAS VARIABLES DINÁMICAS
    public $tipoAsignacionMasiva = 'backoffice'; // Por defecto iniciará en BackOffice
    public $gestorIdSeleccionado = '';

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $filtro_manzana = '';

    #[Url(keep: true)]
    public $filtro_activo = '1';

    #[Url(keep: true)]
    public $filtro_observacion_legal = '';

    #[Url(keep: true)]
    public $filtro_observacion_legal = '';

    #[Url(keep: true)]
    public $con_historico = '';

    #[Url(keep: true)]
    public $filtro_lote_entregado = '';

    #[Url(keep: true)]
    public $filtroGestorBackoffice = '';

    #[Url(keep: true)]
    public $estado_backoffice = '';

    #[Url(keep: true)]
    public $estado_gestor_backoffice = '';

    #[Url(keep: true)]
    public $estado_contrato_preeliminar_emitido = '';

    #[Url(keep: true)]
    public $estado_firma_contrato_firmado = '';

    #[Url(keep: true)]
    public $grupo = '';

    #[Url(keep: true)]
    public $filtro_confirmacion = '';

    #[Url(keep: true)]
    public $filtro_invitacion = '';

    #[Url(keep: true)]
    public $perPage = 20;

    #[Url(keep: true)]
    public $gestor_id = '';

    #[Url(keep: true)]
    public $estado_cliente_id = '';

    #[Url(keep: true)]
    public $gestor_legal_id = '';

    #[Url(as: 'firma_desde')]
    public $fechaFirmaDesde = '';

    #[Url(as: 'firma_hasta')]
    public $fechaFirmaHasta = '';

    #[Url(as: 'generacion_desde')]
    public $fechaGeneracionDesde = '';

    #[Url(as: 'generacion_hasta')]
    public $fechaGeneracionHasta = '';

    public array $stats = [];

    // Catálogos
    public $proyectos = [];
    public $usuarios = [];
    public $estados_cliente = [];
    public $gestoresLegales = [];
    public $gestoresBackofficeList = []; // <-- NUEVO CATÁLOGO
    public $accionObservacionLegal = '';
    // ============================================================
    //                          LIFECYCLE
    // ============================================================

    public function mount($id)
    {
        $this->evento          = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos       = $this->evento->proyectos;
        $this->usuarios        = \App\Models\User::role(['asesor-backoffice', 'supervisor-backoffice'])->get();
        $this->estados_cliente = \App\Models\EntregaFestEstadoCliente::where('activo', true)->orderBy('nombre')->get();
        $this->gestoresLegales = \App\Models\User::where('activo', true)->whereHas('areas', fn($q) => $q->where('area_user.area_id', 3))->orderBy('name')->get();
        $this->gestoresBackofficeList = \App\Models\User::where('activo', true)->whereHas('areas', fn($q) => $q->where('area_user.area_id', 2))->orderBy('name')->get();

        $this->cargarStats();
    }

    public function updated($property): void
    {
        // Al cambiar de página o filtro, solo desmarcamos visualmente el "Seleccionar Todo"
        // pero MANTENEMOS los prospectos que el usuario ya había seleccionado en memoria.
        if ($property === 'paginators.page' || in_array($property, $this->propiedadesFiltro)) {
            $this->selectAll = false;
        }

        if (!in_array($property, $this->propiedadesFiltro)) {
            return;
        }

        $this->resetPage();
        $this->validarRangoFechas();
        $this->cargarStats();
    }

    public function updatedProyectoId($value)
    {
        $this->filtro_manzana = ''; // Limpiamos la manzana elegida al cambiar de proyecto
        $this->resetPage();         // Regresamos a la primera página de la tabla
    }

    // ============================================================
    //            MÉTODOS DE SELECCIÓN Y ASIGNACIÓN MASIVA
    // ============================================================

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Selecciona solo los prospectos de la página actual
            $this->selectedProspectos = ProspectoEntregaFest::query()
                ->filtrado($this->filtrosActivos())
                ->orderBy('nombres', 'asc')
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedProspectos = [];
        }
    }

    public function updatedTipoAsignacionMasiva()
    {
        $this->gestorIdSeleccionado = ''; // Limpiamos el gestor cuando cambian de área
    }

    public function toggleModoAsignacionMasiva()
    {
        $this->modoAsignacionMasiva = !$this->modoAsignacionMasiva;

        // Si el usuario apaga el modo, limpiamos todo
        if (!$this->modoAsignacionMasiva) {
            $this->reset(['selectedProspectos', 'selectAll', 'gestorIdSeleccionado', 'tipoAsignacionMasiva']);
        }
    }

    public function seleccionarTodosLosFiltrados()
    {
        // Selecciona TODOS los prospectos del filtro activo ignorando la paginación
        $this->selectedProspectos = ProspectoEntregaFest::query()
            ->filtrado($this->filtrosActivos())
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $this->selectAll = true;
    }

    public function asignarGestorMasivo()
    {
        // 1. Validamos que existan prospectos seleccionados
        if (empty($this->selectedProspectos)) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => '¡Error en la Asignación!',
                'text'  => 'Debe seleccionar al menos un prospecto.',
            ]);
            return;
        }

        // 2. Validamos según el TIPO de acción masiva elegida
        if ($this->tipoAsignacionMasiva === 'observacion_legal') {

            // Si es Observación Legal, solo exigimos que haya elegido Activar o Desactivar
            if ($this->accionObservacionLegal === '') {
                $this->dispatch('alertaLivewire', [
                    'type'  => 'error',
                    'title' => '¡Falta selección!',
                    'text'  => 'Seleccione si desea Activar o Desactivar la observación legal.',
                ]);
                return;
            }

        } else {

            // Si NO es observación legal (o sea, es Backoffice o Legal), entonces SÍ exigimos un Gestor
            if (empty($this->gestorIdSeleccionado)) {
                $this->dispatch('alertaLivewire', [
                    'type'  => 'error',
                    'title' => '¡Error en la Asignación!',
                    'text'  => 'Debe seleccionar un gestor destino.',
                ]);
                return;
            }

        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Obtenemos los prospectos a actualizar
            $prospectos = ProspectoEntregaFest::whereIn('id', $this->selectedProspectos)->get();
            $mensajeExito = '';

            foreach ($prospectos as $prospecto) {
                // PROTECCIÓN: No editamos masivamente registros históricos/inactivos
                if (!$prospecto->activo) {
                    continue;
                }

                $dataToUpdate = []; // Aquí guardaremos dinámicamente lo que vamos a cambiar

                // LÓGICA A: OBSERVACIÓN LEGAL
                if ($this->tipoAsignacionMasiva === 'observacion_legal') {
                    $valorBooleano = $this->accionObservacionLegal === '1';
                    $dataToUpdate['observacion_legal'] = $valorBooleano;

                    $mensajeExito = $valorBooleano
                        ? 'Observación Legal ACTIVADA para los seleccionados.'
                        : 'Observación Legal DESACTIVADA para los seleccionados.';
                }
                // LÓGICA B: ASIGNAR GESTOR
                else {
                    $columnaDestino = $this->tipoAsignacionMasiva === 'legal' ? 'gestor_legal_id' : 'gestor_backoffice_id';
                    $columnaFecha   = $this->tipoAsignacionMasiva === 'legal' ? 'legal_fecha_asignacion' : 'gestor_fecha_asignacion';
                    $ahora = now();

                    $dataToUpdate[$columnaDestino] = $this->gestorIdSeleccionado;

                    // Si el gestor es distinto al que ya tenía, actualizamos la fecha de asignación
                    if ($prospecto->{$columnaDestino} != $this->gestorIdSeleccionado) {
                        $dataToUpdate[$columnaFecha] = $ahora;
                    }

                    $mensajeExito = 'Los gestores han sido asignados correctamente y se sincronizó el historial.';
                }

                // A) Actualizamos el ticket en este evento
                $prospecto->update($dataToUpdate);

                // B) SINCRONIZAMOS CON LA TABLA MAESTRA (Histórico)
                if ($prospecto->prospecto_historico_id) {
                    $prospecto->prospectoHistorico->update($dataToUpdate);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Reseteamos las variables de la interfaz
            $this->reset(['selectedProspectos', 'selectAll', 'gestorIdSeleccionado', 'modoAsignacionMasiva', 'tipoAsignacionMasiva', 'accionObservacionLegal']);

            // Actualizamos contadores si existe la función
            // $this->cargarStats();

            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => '¡Acción Exitosa!',
                'text'  => $mensajeExito,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error("[ASIGNACIÓN MASIVA] Error: " . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => 'Error de Acción Masiva',
                'text'  => 'Ocurrió un problema al guardar los cambios masivos.',
            ]);
        }
    }

    // ============================================================
    //                       FILTROS / HELPERS
    // ============================================================

    protected function filtrosActivos(): array
    {
        return [
            'evento_id'                            => $this->evento->id,
            'buscar'                               => $this->buscar,
            'proyecto_id'                          => $this->proyecto_id,
            'filtro_manzana'                       => $this->filtro_manzana,
            'filtro_observacion_legal'             => $this->filtro_observacion_legal,
            'con_historico'                        => $this->con_historico,
            'filtro_lote_entregado'                => $this->filtro_lote_entregado,
            'filtro_gestor_backoffice'             => $this->filtroGestorBackoffice,
            'estado_backoffice'                    => $this->estado_backoffice,
            'estado_gestor_backoffice'             => $this->estado_gestor_backoffice,
            'estado_contrato_preeliminar_emitido'  => $this->estado_contrato_preeliminar_emitido,
            'estado_firma_contrato_firmado'        => $this->estado_firma_contrato_firmado,
            'grupo'                                => $this->grupo,
            'filtro_confirmacion'                  => $this->filtro_confirmacion,
            'filtro_invitacion'                    => $this->filtro_invitacion,
            'gestor_id'                            => $this->gestor_id,
            'estado_cliente_id'                    => $this->estado_cliente_id,
            'gestor_legal_id'                      => $this->gestor_legal_id,
            'fecha_firma_desde'                    => $this->fechaFirmaDesde,
            'fecha_firma_hasta'                    => $this->fechaFirmaHasta,
            'fecha_generacion_desde'               => $this->fechaGeneracionDesde,
            'fecha_generacion_hasta'               => $this->fechaGeneracionHasta,
        ];
    }

    /**
     * Garantiza que "Desde" sea siempre <= "Hasta".
     */
    protected function validarRangoFechas(): void
    {
        if (
            $this->fechaFirmaDesde && $this->fechaFirmaHasta &&
            $this->fechaFirmaDesde > $this->fechaFirmaHasta
        ) {

            $this->fechaFirmaHasta = $this->fechaFirmaDesde;
            session()->flash(
                'aviso_filtro',
                'La fecha "Hasta" no puede ser menor que "Desde". Se ajustó automáticamente.'
            );
        }

        if (
            $this->fechaGeneracionDesde && $this->fechaGeneracionHasta &&
            $this->fechaGeneracionDesde > $this->fechaGeneracionHasta
        ) {
            $this->fechaGeneracionHasta = $this->fechaGeneracionDesde;
            session()->flash(
                'aviso_filtro',
                'La fecha "Hasta" de generación no puede ser menor que "Desde". Se ajustó automáticamente.'
            );
        }
    }

    public function limpiarRangoFechas(): void
    {
        $this->reset(['fechaFirmaDesde', 'fechaFirmaHasta', 'fechaGeneracionDesde', 'fechaGeneracionHasta']);
        $this->resetPage();
        $this->cargarStats();
    }

    public function resetFiltros(): void
    {
        $this->reset($this->propiedadesFiltro);
        $this->resetPage();
        $this->cargarStats();
    }

    public function cargarStats(): void
    {
        $base = ProspectoEntregaFest::filtrado($this->filtrosActivos());

        $this->stats = [
            'total'         => (clone $base)->count(),
            'preinvitacion' => (clone $base)->where('preinvitacion_confirmada', 1)->count(),
            'invitacion'    => (clone $base)->where('invitacion_confirmada', 1)->count(),
            'backoffice'    => (clone $base)->where('estado_backoffice', 'CONFORME')->count(),
            'contrato'      => (clone $base)->where('estado_contrato_preeliminar_emitido', 'CONFORME')->count(),
            'firmados'      => (clone $base)->whereNotNull('fecha_firma')->count(),
        ];
    }

    // ============================================================
    //                          EXPORTS
    // ============================================================

    public function exportExcelFiltro()
    {
        $this->authorize('prospecto.exportar-filtro');

        return Excel::download(
            new EntregaFestProspectoExport($this->filtrosActivos()),
            'prospectos_filtrados_' . $this->evento->codigo . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('prospecto.exportar-todo');

        return Excel::download(
            new EntregaFestProspectoExport(['evento_id' => $this->evento->id]),
            'prospectos_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    // ============================================================
    //                          ACCIONES
    // ============================================================

    public function enviarPreInvitacion(): void
    {
        EntregaFestPreInvitacion::dispatch($this->evento);
        $this->dispatch('alertaLivewire', [
            'type'  => 'success',
            'title' => '¡Solicitud de envío procesada!',
            'text'  => 'Se ha enviado la orden de envío masivo de "Pre-invitación" a n8n',
        ]);
    }

    public function enviarInvitacion(): void
    {
        EntregaFestAsistenciaInvitacionMasivo::dispatch($this->evento);
        $this->dispatch('alertaLivewire', [
            'type'  => 'success',
            'title' => '¡Solicitud de envío procesada!',
            'text'  => 'Se ha enviado la orden de envío masivo de "Invitación" a n8n',
        ]);
    }

    // ============================================================
    //                           RENDER
    // ============================================================

    public function render()
    {
        // 1. Calculamos las manzanas dinámicamente basadas en el Proyecto actual
        $manzanasList = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->whereNotNull('manzana')
            ->where('manzana', '!=', '')
            ->distinct()
            ->orderBy('manzana')
            ->pluck('manzana');

        $items = ProspectoEntregaFest::query()
            ->with([
                'proyecto',
                'reubicadoProyecto',
                'user',
                'invitado',
                'gestor',
                'copropietarios',
                'historialComunicaciones',
                'copropietarios.historialComunicaciones',
                'estadoCliente',
            ])
            ->filtrado($this->filtrosActivos())
            ->orderBy('nombres', 'asc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto', [
            'items' => $items,
            'manzanasList' => $manzanasList, // 🛑 DEBES AÑADIR ESTA LÍNEA AQUÍ
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
