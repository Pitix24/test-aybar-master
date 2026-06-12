<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Events\EntregaFest\EntregaFestAsistenciaInvitacion;
use App\Events\EntregaFest\EntregaFestCitaRecordatorio;
use App\Events\EntregaFest\EntregaFestContratoPreliminar;
use App\Models\AuditoriaProspectoContrato;
use App\Models\CopropietarioEntregaFest;
use App\Models\EntregaFest;
use App\Models\InvitadoEnvioEntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\EntregaFestEstadoCliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Evaluar Prospecto - Entrega Fest')]
class EntregaFestProspectoEditar extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public ProspectoEntregaFest $prospecto;

    // Campos del prospecto
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '';
    public $lote = '', $manzana = '', $estado_cliente_id = '';
    // Reubicación
    public $reubicado_proyecto_id = '', $reubicado_lote = '', $reubicado_manzana = '';

    // BackOffice
    public $grupo, $gestor_backoffice_id = '', $gestor_fecha_asignacion, $fecha_culminacion_eecc, $link_carpeta_eecc, $link_eecc_firmado;
    public $validador_backoffice_id = '', $fecha_validacion_eecc, $estado_backoffice;
    public $estado_gestor_backoffice, $observacion_gestor_backoffice;
    public $responsable_llamada_id = '', $responsable_llamada_fecha_asignacion;

    // Legal
    public $gestor_legal_id;
    public $legal_fecha_asignacion;
    public $observacion_gestor_legal;

    public $validador_legal_id;
    public $fecha_firma_presencial;     // 🆕 manual
    public $fecha_validacion_firma;     // 🆕 auto
    public $estado_contrato_preeliminar_emitido, $estado_firma_contrato_firmado;
    public $fecha_firma, $fecha_generacion_contrato;

    // Archivos
    public $archivo_contrato_preeliminar;

    public $link_preinvitacion = '';
    public $link_invitacion = '';
    public $link_cita_contrato = '';

    public $proyectos = [];
    public $estados_cliente = [];

    // ── Copropietarios ──────────────────────────────────────────────────
    public $copropietarios = [];

    // Modo: null = lista, 'crear' = formulario nuevo, 'editar' = editando fila
    public $cop_modo = null;
    public $cop_editando_id = null;

    // Campos del formulario copropietario
    public $cop_dni = '';
    public $cop_nombres = '';
    public $cop_email = '';
    public $cop_celular = '';

    // ────────────────────────────────────────────────────────────────────

    protected function rules()
    {
        return [
            // Datos básicos del prospecto
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            //'email' => 'required|email|max:255',
            //'celular' => 'required|string|max:20',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',

            // BackOffice — Asesor
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'gestor_fecha_asignacion' => 'nullable|date',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',
            'estado_gestor_backoffice' => 'required|in:PENDIENTE,BANCARIZAR,PENALIDAD,OBSERVADO,CONFORME,VIGENTE',
            'observacion_gestor_backoffice' => 'nullable|string',
            'responsable_llamada_id' => 'nullable|exists:users,id',
            'responsable_llamada_fecha_asignacion' => 'nullable|date',

            // BackOffice — Supervisor
            'validador_backoffice_id' => 'nullable|exists:users,id',
            'fecha_validacion_eecc' => 'nullable|date',
            'estado_backoffice' => 'required|in:PENDIENTE,BANCARIZAR,PENALIDAD,OBSERVADO,CONFORME,VIGENTE',

            // Legal — Asesor
            'estado_contrato_preeliminar_emitido' => 'required|in:PENDIENTE,GENERADO,OBSERVADO,CONFORME',

            // Legal — Supervisor
            'estado_firma_contrato_firmado' => 'required|in:PENDIENTE,FIRMADO',
            'fecha_firma' => 'nullable|date',
            'fecha_generacion_contrato' => 'nullable|date',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'proyecto_id' => 'proyecto',
            'dni' => 'DNI',
            'nombres' => 'nombres completos',
            'email' => 'correo electrónico',
            'celular' => 'número de celular',
            'grupo' => 'grupo backoffice',
            'estado_backoffice' => 'estado backoffice',
            'estado_contrato_preeliminar_emitido' => 'estado contrato preliminar',
            'estado_firma_contrato_firmado' => 'estado firma contrato',
            'responsable_llamada_id' => 'responsable de llamada',
            'responsable_llamada_fecha_asignacion' => 'fecha de asignación de llamada',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id, $prospectoId)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->prospecto = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)->findOrFail($prospectoId);

        $this->proyecto_id = $this->prospecto->proyecto_id;
        $this->dni = $this->prospecto->dni;
        $this->nombres = $this->prospecto->nombres;
        $this->email = $this->prospecto->email;
        $this->celular = $this->prospecto->celular;

        $this->lote = $this->prospecto->lote;
        $this->manzana = $this->prospecto->manzana;
        $this->estado_cliente_id = $this->prospecto->estado_cliente_id;

        // Reubicación (no sobrescribe los datos originales)
        $this->reubicado_proyecto_id = $this->prospecto->reubicado_proyecto_id ?? $this->prospecto->proyecto_id;
        $this->reubicado_lote = $this->prospecto->reubicado_lote;
        $this->reubicado_manzana = $this->prospecto->reubicado_manzana;

        // BackOffice
        $this->grupo = $this->prospecto->grupo;
        $this->gestor_backoffice_id = $this->prospecto->gestor_backoffice_id;
        $this->gestor_fecha_asignacion = $this->prospecto->gestor_fecha_asignacion
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->gestor_fecha_asignacion)) : null;
        $this->fecha_culminacion_eecc = $this->prospecto->fecha_culminacion_eecc
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_culminacion_eecc)) : null;
        $this->link_carpeta_eecc = $this->prospecto->link_carpeta_eecc;
        $this->link_eecc_firmado = $this->prospecto->link_eecc_firmado;
        $this->estado_gestor_backoffice = $this->prospecto->estado_gestor_backoffice;
        $this->observacion_gestor_backoffice = $this->prospecto->observacion_gestor_backoffice;

        $this->responsable_llamada_id = $this->prospecto->responsable_llamada_id;
        $this->responsable_llamada_fecha_asignacion = $this->prospecto->responsable_llamada_fecha_asignacion
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->responsable_llamada_fecha_asignacion)) : null;

        $this->validador_backoffice_id = $this->prospecto->validador_backoffice_id;
        $this->fecha_validacion_eecc = $this->prospecto->fecha_validacion_eecc
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_validacion_eecc)) : null;
        $this->estado_backoffice = $this->prospecto->estado_backoffice;

        // ============ Legal: Gestor ============
        $this->gestor_legal_id = $this->prospecto->gestor_legal_id;
        $this->legal_fecha_asignacion = $this->prospecto->legal_fecha_asignacion
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->legal_fecha_asignacion)) : null;
        $this->observacion_gestor_legal = $this->prospecto->observacion_gestor_legal;

        // ============ Legal: Validador / Firma ============
        $this->validador_legal_id = $this->prospecto->validador_legal_id;
        $this->fecha_firma_presencial = $this->prospecto->fecha_firma_presencial
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_firma_presencial)) : null;
        $this->fecha_validacion_firma = $this->prospecto->fecha_validacion_firma
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_validacion_firma)) : null;

        $this->estado_contrato_preeliminar_emitido = $this->prospecto->estado_contrato_preeliminar_emitido;
        $this->estado_firma_contrato_firmado = $this->prospecto->estado_firma_contrato_firmado;
        $this->fecha_firma = $this->prospecto->fecha_firma
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_firma)) : null;
        $this->fecha_generacion_contrato = $this->prospecto->fecha_generacion_contrato
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_generacion_contrato)) : null;

        $this->link_preinvitacion = route('entrega-fest.pre-invitacion.propietario', [
            'slug' => $this->evento->slug,
            'propietarioId' => $this->prospecto->id
        ]);

        $this->link_invitacion = route('entrega-fest.asistencia-invitacion.propietario', [
            'slug' => $this->evento->slug,
            'propietarioId' => $this->prospecto->id,
        ]);

        // 🆕 Link de Cita de Contrato
        $this->link_cita_contrato = route('entrega-fest.cita-agendar.propietario', [
            'slug' => $this->evento->slug,
            'propietarioId' => $this->prospecto->id,
        ]);

        $this->proyectos = $this->evento->proyectos;
        $this->estados_cliente = EntregaFestEstadoCliente::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $this->cargarCopropietarios();
    }

    // ════════════════════════════════════════════════════════════════════
    // COPROPIETARIOS
    // ════════════════════════════════════════════════════════════════════

    public function cargarCopropietarios(): void
    {
        $this->copropietarios = CopropietarioEntregaFest::where('prospecto_entrega_fest_id', $this->prospecto->id)
            ->orderBy('nombres')
            ->get()
            ->toArray();
    }

    public function abrirFormCrear(): void
    {
        $this->resetCopropietarioForm();
        $this->cop_modo = 'crear';
    }

    public function cancelarCopropietario(): void
    {
        $this->resetCopropietarioForm();
        $this->cop_modo = null;
        $this->cop_editando_id = null;
    }

    private function resetCopropietarioForm(): void
    {
        $this->cop_dni = '';
        $this->cop_nombres = '';
        $this->cop_email = '';
        $this->cop_celular = '';
        $this->resetErrorBag(['cop_dni', 'cop_nombres', 'cop_email', 'cop_celular']);
    }

    private function reglasCoprietario(bool $esEdicion = false): array
    {
        $dniUnique = 'unique:copropietario_entrega_fests,dni,NULL,id,prospecto_entrega_fest_id,' . $this->prospecto->id;

        if ($esEdicion && $this->cop_editando_id) {
            $dniUnique = 'unique:copropietario_entrega_fests,dni,' . $this->cop_editando_id . ',id,prospecto_entrega_fest_id,' . $this->prospecto->id;
        }

        return [
            'cop_dni' => ['required', 'string', 'max:15', $dniUnique],
            'cop_nombres' => 'required|string|max:255',
            'cop_email' => 'nullable|email|max:255',
            'cop_celular' => 'nullable|string|max:20',
        ];
    }

    private function atributosCopropietario(): array
    {
        return [
            'cop_dni' => 'DNI del copropietario',
            'cop_nombres' => 'nombres del copropietario',
            'cop_email' => 'correo del copropietario',
            'cop_celular' => 'celular del copropietario',
        ];
    }

    public function storeCopropietario(): void
    {
        $this->authorize('prospecto.editar');

        $this->validate($this->reglasCoprietario(), [], $this->atributosCopropietario());

        try {
            CopropietarioEntregaFest::create([
                'prospecto_entrega_fest_id' => $this->prospecto->id,
                'dni' => trim($this->cop_dni),
                'nombres' => trim($this->cop_nombres),
                'email' => trim($this->cop_email) ?: null,
                'celular' => trim($this->cop_celular) ?: null,
            ]);

            $this->cancelarCopropietario();
            $this->cargarCopropietarios();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Agregado!',
                'text' => 'Copropietario registrado correctamente.',
            ]);
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error('[COPROPIETARIO CREAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo guardar el copropietario.']);
        }
    }

    public function editarCopropietario(int $id): void
    {
        $cop = CopropietarioEntregaFest::where('prospecto_entrega_fest_id', $this->prospecto->id)->findOrFail($id);

        $this->cop_editando_id = $cop->id;
        $this->cop_dni = $cop->dni;
        $this->cop_nombres = $cop->nombres;
        $this->cop_email = $cop->email ?? '';
        $this->cop_celular = $cop->celular ?? '';
        $this->cop_modo = 'editar';
    }

    public function updateCopropietario(): void
    {
        $this->authorize('prospecto.editar');

        $this->validate($this->reglasCoprietario(true), [], $this->atributosCopropietario());

        $cop = CopropietarioEntregaFest::where('prospecto_entrega_fest_id', $this->prospecto->id)
            ->findOrFail($this->cop_editando_id);

        try {
            $cop->update([
                'dni' => trim($this->cop_dni),
                'nombres' => trim($this->cop_nombres),
                'email' => trim($this->cop_email) ?: null,
                'celular' => trim($this->cop_celular) ?: null,
            ]);

            $this->cancelarCopropietario();
            $this->cargarCopropietarios();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Copropietario actualizado correctamente.',
            ]);
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error('[COPROPIETARIO EDITAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo actualizar.']);
        }
    }

    public function eliminarCopropietario(int $id): void
    {
        $this->authorize('prospecto.editar');

        $cop = CopropietarioEntregaFest::where('prospecto_entrega_fest_id', $this->prospecto->id)->findOrFail($id);

        // Si ya tiene invitación, no se puede eliminar
        if ($cop->invitado) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No permitido',
                'text' => 'Este copropietario ya tiene una invitación generada. Elimínala primero.',
            ]);
            return;
        }

        try {
            $cop->delete();
            $this->cargarCopropietarios();
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'Copropietario eliminado.',
            ]);
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error('[COPROPIETARIO ELIMINAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo eliminar.']);
        }
    }

    // ════════════════════════════════════════════════════════════════════
    // PROSPECTO — helpers de actualización
    // ════════════════════════════════════════════════════════════════════

    private function registrarAuditoriaContrato(string $accion, ?array $mediaData = null, array $payload = []): void
    {
        try {
            AuditoriaProspectoContrato::create([
                'prospecto_entrega_fest_id' => $this->prospecto->id,
                'user_id' => Auth::id(),
                'media_id' => $mediaData['id'] ?? null,
                'accion' => $accion,
                'collection_name' => 'contrato-preliminar',
                'file_name' => $mediaData['file_name'] ?? null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'payload' => $payload,
            ]);
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error('[AUDITORIA CONTRATO PRELIMINAR] Error registrando auditoria: ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'prospecto_id' => $this->prospecto->id,
                'accion' => $accion,
            ]);
        }
    }

    private function handleUpdate(array $data, string $logContext)
    {
        $this->authorize('prospecto-entrega-fest.editar');

        $antes = [];
        foreach ($data as $campo => $valorNuevo) {
            $antes[$campo] = $this->prospecto->{$campo} ?? null;
        }

        try {
            DB::beginTransaction();
            $this->prospecto->update($data);
            DB::commit();

            $cambios = [];
            foreach ($data as $campo => $valorNuevo) {
                if (($antes[$campo] ?? null) != $valorNuevo) {
                    $cambios[$campo] = [
                        'antes' => $antes[$campo] ?? null,
                        'despues' => $valorNuevo,
                    ];
                }
            }

            Log::channel('entrega-fest')->info("[$logContext] Actualización exitosa", [
                'usuario_id' => Auth::id(),
                'prospecto_id' => $this->prospecto->id,
                'cambios' => $cambios,
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Información actualizada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[$logContext] Error: " . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'prospecto_id' => $this->prospecto->id,
                'datos' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la información.'
            ]);
        }
    }

    public function updateProspecto()
    {
        $rules = [
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',
            'estado_cliente_id' => 'required|exists:entrega_fest_estado_clientes,id',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'proyecto_id' => $this->proyecto_id,
            'dni' => $this->dni,
            'nombres' => trim($this->nombres),
            'email' => trim($this->email),
            'celular' => trim($this->celular),
            'lote' => $this->lote,
            'manzana' => $this->manzana,
            'estado_cliente_id' => $this->estado_cliente_id,
        ], 'PROSPECTO EDITAR - BASICO');
    }

    public function updateReubicacion()
    {
        $this->authorize('prospecto.editar');

        $rules = [
            'reubicado_proyecto_id' => 'nullable|exists:proyectos,id',
            'reubicado_lote' => 'nullable|string|max:20',
            'reubicado_manzana' => 'nullable|string|max:20',
        ];

        $this->validate($rules);

        $data = [
            'reubicado_proyecto_id' => $this->reubicado_proyecto_id ?: null,
            'reubicado_lote' => $this->reubicado_lote ?: null,
            'reubicado_manzana' => $this->reubicado_manzana ?: null,
        ];

        $this->handleUpdate($data, 'PROSPECTO EDITAR - REUBICACION');
    }

    public function updateBackoffice()
    {
        $rules = [
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',
            'estado_gestor_backoffice' => 'required|in:PENDIENTE,BANCARIZAR,PENALIDAD,OBSERVADO,CONFORME,VIGENTE',
            'observacion_gestor_backoffice' => 'nullable|string',
        ];

        $this->validate($rules);

        // Si se acaba de asignar un gestor, actualizamos la fecha de asignación
        if ($this->gestor_backoffice_id && $this->prospecto->gestor_backoffice_id != $this->gestor_backoffice_id) {
            $this->gestor_fecha_asignacion = now()->format('Y-m-d\TH:i');
        }

        $this->handleUpdate([
            'grupo' => $this->grupo,
            'gestor_backoffice_id' => $this->gestor_backoffice_id ?: null,
            'gestor_fecha_asignacion' => $this->gestor_fecha_asignacion,
            'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
            'link_carpeta_eecc' => $this->link_carpeta_eecc,
            'link_eecc_firmado' => $this->link_eecc_firmado,
            'estado_gestor_backoffice' => $this->estado_gestor_backoffice,
            'observacion_gestor_backoffice' => $this->observacion_gestor_backoffice,
        ], 'PROSPECTO EDITAR - BACKOFFICE');
    }

    public function updateLlamada()
    {
        $rules = [
            'responsable_llamada_id' => 'nullable|exists:users,id',
            'responsable_llamada_fecha_asignacion' => 'nullable|date',
        ];

        $this->validate($rules);

        // Si se acaba de asignar un responsable, actualizamos la fecha de asignación
        if ($this->responsable_llamada_id && $this->prospecto->responsable_llamada_id != $this->responsable_llamada_id) {
            $this->responsable_llamada_fecha_asignacion = now()->format('Y-m-d\TH:i');
        }

        $this->handleUpdate([
            'responsable_llamada_id' => $this->responsable_llamada_id ?: null,
            'responsable_llamada_fecha_asignacion' => $this->responsable_llamada_fecha_asignacion,
        ], 'PROSPECTO EDITAR - LLAMADA');
    }

    public function updateBackofficeSupervisor()
    {
        // Auto-asignar si están vacíos al momento de validar
        $this->validador_backoffice_id = Auth::id();
        $this->fecha_validacion_eecc = now()->format('Y-m-d\TH:i');

        $rules = [
            'validador_backoffice_id' => 'required|exists:users,id',
            'fecha_validacion_eecc' => 'required|date',
            'estado_backoffice' => 'required|in:PENDIENTE,BANCARIZAR,PENALIDAD,OBSERVADO,CONFORME,VIGENTE',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'validador_backoffice_id' => $this->validador_backoffice_id,
            'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
            'estado_backoffice' => $this->estado_backoffice,
        ], 'PROSPECTO EDITAR - BACKOFFICE');

        // Disparar lógica de bancarización e invitaciones
        if (in_array($this->estado_backoffice, ['CONFORME', 'BANCARIZAR', 'VIGENTE'])) {

            if ($this->estado_backoffice === 'CONFORME') {
                \App\Models\ProspectoBancarizacionEntregaFest::where('prospecto_entrega_fest_id', $this->prospecto->id)
                    ->where('entrega_fest_id', $this->evento->id)
                    ->update(['estado' => 'BANCARIZADO']);
            }

            EntregaFestAsistenciaInvitacion::dispatch($this->prospecto);
        }
    }

    public function updateLegal()
    {
        // 1. PDF requerido cuando estado = CONFORME
        $isConforme = ($this->estado_contrato_preeliminar_emitido === 'CONFORME');
        $hasFile    = $this->prospecto->hasMedia('contrato-preliminar');

        if ($isConforme && !$hasFile && !$this->archivo_contrato_preeliminar) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => '¡Contrato requerido!',
                'text'  => 'Para establecer el estado en CONFORME, debes adjuntar obligatoriamente el contrato preliminar en formato PDF.',
            ]);
            return;
        }

        // 2. Validación
        $rules = [
            'gestor_legal_id'                     => 'nullable|exists:users,id',
            'observacion_gestor_legal'            => 'nullable|string',
            'estado_contrato_preeliminar_emitido' => 'required|in:PENDIENTE,GENERADO,OBSERVADO,CONFORME',
            'archivo_contrato_preeliminar'        => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
        $this->validate($rules);

        // 🆕 3. Detectar PRIMERA transición a CONFORME (criterio: aún no hay fecha registrada)
        $primeraVezConforme = ($isConforme && !$this->prospecto->fecha_generacion_contrato);

        // 4. legal_fecha_asignacion: actualizar si cambió el gestor
        if ($this->gestor_legal_id && $this->prospecto->gestor_legal_id != $this->gestor_legal_id) {
            $this->legal_fecha_asignacion = now()->format('Y-m-d\TH:i');
        }

        // 5. Subir PDF (lógica existente, sin cambios)
        if ($this->archivo_contrato_preeliminar) {
            try {
                $mediaAnterior     = $this->prospecto->getFirstMedia('contrato-preliminar');
                $mediaAnteriorData = $mediaAnterior ? [
                    'id'        => $mediaAnterior->id,
                    'file_name' => $mediaAnterior->file_name,
                    'size'      => $mediaAnterior->size,
                ] : null;

                $this->prospecto->clearMediaCollection('contrato-preliminar');

                $mediaNueva = $this->prospecto->addMedia($this->archivo_contrato_preeliminar->getRealPath())
                    ->usingFileName('CONTRATO_PRELIMINAR_' . $this->prospecto->dni . '.pdf')
                    ->toMediaCollection('contrato-preliminar');

                $this->registrarAuditoriaContrato($mediaAnterior ? 'reemplazado' : 'subido', [
                    'id'        => $mediaNueva->id,
                    'file_name' => $mediaNueva->file_name,
                ], [
                    'media_anterior' => $mediaAnteriorData,
                ]);

                Log::channel('entrega-fest')->info('[LEGAL UPLOAD] Contrato preliminar guardado', [
                    'usuario_id'   => Auth::id(),
                    'prospecto_id' => $this->prospecto->id,
                    'accion'       => $mediaAnterior ? 'reemplazado' : 'subido',
                    'media_id'     => $mediaNueva->id,
                ]);

                $this->archivo_contrato_preeliminar = null;
                $this->prospecto->refresh();
            } catch (\Exception $e) {
                Log::channel('entrega-fest')->error('[LEGAL UPLOAD] ' . $e->getMessage());
                $this->dispatch('alertaLivewire', [
                    'type'  => 'error',
                    'title' => 'Error de carga',
                    'text'  => 'No se pudo guardar el archivo PDF. Inténtelo de nuevo.',
                ]);
                return;
            }
        }

        // 6. Construir payload
        $payload = [
            'gestor_legal_id'                     => $this->gestor_legal_id ?: null,
            'legal_fecha_asignacion'              => $this->legal_fecha_asignacion,
            'observacion_gestor_legal'            => $this->observacion_gestor_legal,
            'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
        ];

        // 🆕 7. Si es la PRIMERA vez en CONFORME, registramos fecha automáticamente
        if ($primeraVezConforme) {
            $ahora = now();
            $payload['fecha_generacion_contrato'] = $ahora;
            $this->fecha_generacion_contrato      = $ahora->format('Y-m-d\TH:i');

            Log::channel('entrega-fest')->info('[LEGAL] Contrato Preliminar marcado CONFORME por primera vez', [
                'prospecto_id'              => $this->prospecto->id,
                'usuario_id'                => Auth::id(),
                'fecha_generacion_contrato' => $ahora->toDateTimeString(),
            ]);
        }

        // 8. Persistir
        $this->handleUpdate($payload, 'PROSPECTO EDITAR - LEGAL');

        // 🆕 9. Mensaje informativo si fue primera vez (el flujo n8n se dispara solo, ya existe)
        if ($primeraVezConforme) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => '¡Contrato Conforme registrado!',
                'text'  => 'Se registró automáticamente la fecha de generación del contrato.',
            ]);
        }
    }

    public function updateLegalSupervisor()
    {
        // 🛡️ VALIDACIÓN CRUZADA 1: Para FIRMADO se requiere gestor asignado
        if ($this->estado_firma_contrato_firmado === 'FIRMADO' && !$this->prospecto->gestor_legal_id) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Gestor Legal requerido',
                'text'  => 'No puedes confirmar la firma sin haber asignado un Gestor Legal previamente.',
            ]);
            return;
        }

        // 🛡️ VALIDACIÓN CRUZADA 2: El contrato preliminar debe estar CONFORME
        if (
            $this->estado_firma_contrato_firmado === 'FIRMADO'
            && !in_array($this->prospecto->estado_contrato_preeliminar_emitido, ['CONFORME'])
        ) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Contrato Preliminar requerido',
                'text'  => 'No puedes confirmar la firma sin haber confirmado el contrato preliminar.',
            ]);
            return;
        }

        // 🛡️ VALIDACIÓN CRUZADA 3: Si marca FIRMADO, debe llenar la fecha presencial
        if ($this->estado_firma_contrato_firmado === 'FIRMADO' && !$this->fecha_firma_presencial) {
            $this->dispatch('alertaLivewire', [
                'type'  => 'warning',
                'title' => 'Fecha presencial requerida',
                'text'  => 'Debes registrar la fecha real en que se realizó la firma presencial.',
            ]);
            return;
        }

        // 🆕 Auto-registrar validador y fecha de validación en el momento de guardar
        $this->validador_legal_id    = Auth::id();
        $this->fecha_validacion_firma = now()->format('Y-m-d\TH:i');

        $rules = [
            'estado_firma_contrato_firmado' => 'required|in:PENDIENTE,FIRMADO',
            'fecha_firma_presencial'        => 'nullable|date',
            'validador_legal_id'            => 'required|exists:users,id',
            'fecha_validacion_firma'        => 'required|date',
        ];
        $this->validate($rules);

        $this->handleUpdate([
            'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
            'fecha_firma_presencial'        => $this->fecha_firma_presencial,
            'validador_legal_id'            => $this->validador_legal_id,
            'fecha_validacion_firma'        => $this->fecha_validacion_firma,
        ], 'PROSPECTO EDITAR - LEGAL SUPERVISOR');
    }

    public function solicitarEliminarContratoPreliminar()
    {
        $this->authorize('contrato-preliminar.eliminar');

        if (!$this->prospecto->hasMedia('contrato-preliminar')) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Sin archivo',
                'text' => 'El prospecto no tiene contrato preliminar adjunto.',
            ]);
            return;
        }

        $this->dispatch('alertaConfirmar', [
            'event' => 'eliminarContratoPreliminarOn',
            'titulo' => '¿Eliminar contrato preliminar?',
            'texto' => 'Se eliminará el PDF adjunto de forma permanente. Esta acción no se puede deshacer.',
        ]);
    }

    #[On('eliminarContratoPreliminarOn')]
    public function eliminarContratoPreliminarOn()
    {
        $this->authorize('contrato-preliminar.eliminar');

        if (!$this->prospecto->hasMedia('contrato-preliminar')) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Sin archivo',
                'text' => 'El contrato preliminar ya fue eliminado.',
            ]);
            return;
        }

        try {
            $mediaActual = $this->prospecto->getFirstMedia('contrato-preliminar');
            $mediaActualData = $mediaActual ? [
                'id' => $mediaActual->id,
                'file_name' => $mediaActual->file_name,
                'size' => $mediaActual->size,
            ] : null;

            $this->prospecto->clearMediaCollection('contrato-preliminar');

            $this->registrarAuditoriaContrato('eliminado', $mediaActualData, [
                'motivo' => 'eliminacion_manual',
            ]);

            // Restablecer estado del contrato a PENDIENTE
            try {
                $this->prospecto->update(['estado_contrato_preeliminar_emitido' => 'PENDIENTE']);
            } catch (\Exception $e) {
                Log::channel('entrega-fest')->warning('[LEGAL DELETE] No se pudo actualizar estado a PENDIENTE: ' . $e->getMessage(), [
                    'usuario_id' => Auth::id(),
                    'prospecto_id' => $this->prospecto->id,
                ]);
            }

            Log::channel('entrega-fest')->info('[LEGAL DELETE CONTRATO PRELIMINAR] Eliminación exitosa', [
                'usuario_id' => Auth::id(),
                'prospecto_id' => $this->prospecto->id,
                'media_eliminada' => $mediaActualData,
            ]);

            $this->archivo_contrato_preeliminar = null;
            $this->prospecto->refresh();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El contrato preliminar fue eliminado correctamente y su estado quedó en Pendiente.',
            ]);
        } catch (\Exception $e) {
            Log::channel('entrega-fest')->error('[LEGAL DELETE CONTRATO PRELIMINAR] ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'prospecto_id' => $this->prospecto->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el contrato preliminar.',
            ]);
        }
    }

    public function solicitarEliminarProspecto()
    {
        $this->dispatch('alertaConfirmar', [
            'event' => 'eliminarProspectoOn',
            'titulo' => '¿Quieres eliminar este prospecto?',
            'texto' => 'Esta acción eliminará el prospecto y sus datos relacionados. No se puede deshacer.',
        ]);
    }

    #[On('eliminarProspectoOn')]
    public function eliminarProspectoOn()
    {
        $this->authorize('prospecto.eliminar');

        try {
            DB::beginTransaction();

            $prospecto = $this->prospecto->load([
                'copropietarios.invitado.asistencia',
                'copropietarios.invitado.acompanantes',
                'invitado.asistencia',
                'invitado.acompanantes',
            ]);

            $prospecto->historialComunicaciones()->delete();
            $prospecto->bancarizaciones()->delete();
            $prospecto->acompanantes()->delete();

            if ($prospecto->invitado) {
                $this->eliminarInvitado($prospecto->invitado);
            }

            foreach ($prospecto->copropietarios as $copropietario) {
                $copropietario->historialComunicaciones()->delete();

                if ($copropietario->invitado) {
                    $this->eliminarInvitado($copropietario->invitado);
                }

                $copropietario->delete();
            }

            $mediaContrato = $prospecto->getFirstMedia('contrato-preliminar');
            if ($mediaContrato) {
                $mediaContratoData = [
                    'id' => $mediaContrato->id,
                    'file_name' => $mediaContrato->file_name,
                    'size' => $mediaContrato->size,
                ];

                $this->registrarAuditoriaContrato('eliminado_por_prospecto', $mediaContratoData, [
                    'motivo' => 'eliminacion_de_prospecto',
                ]);

                $prospecto->clearMediaCollection('contrato-preliminar');
            }

            $prospecto->delete();

            Log::channel('entrega-fest')->info('[PROSPECTO ELIMINAR] Eliminación exitosa', [
                'usuario_id' => Auth::id(),
                'evento_id' => $this->evento->id,
                'prospecto_id' => $prospecto->id,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'El prospecto y sus relaciones se eliminaron correctamente.',
            ]);

            return redirect()->route('erp.entrega-fest.prospecto.todo', $this->evento->id);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('entrega-fest')->error('[PROSPECTO ELIMINAR] ' . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'evento_id' => $this->evento->id,
                'prospecto_id' => $this->prospecto->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el prospecto.',
            ]);
        }
    }

    protected function eliminarInvitado($invitado): void
    {
        $invitado->asistencia()?->delete();
        $invitado->acompanantes()->delete();
        InvitadoEnvioEntregaFest::where('invitado_entrega_fest_id', $invitado->id)->delete();
        $invitado->delete();
    }

    // ──────────────────────────────────────────────────────────────────
    // RECORDATORIO DE FIRMA
    // ──────────────────────────────────────────────────────────────────

    public function enviarRecordatorioFirma()
    {
        $this->authorize('prospecto.editar');

        // Recargar el prospecto actual desde BD con relaciones necesarias
        $prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto'])
            ->find($this->prospecto->id);

        // Validar condiciones
        if ($prospecto->estado_backoffice !== 'CONFORME') {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No permitido',
                'text' => 'El prospecto no tiene el BackOffice en estado Conforme.',
            ]);
            return;
        }

        if ($prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No permitido',
                'text' => 'El contrato preliminar aún no está Conforme.',
            ]);
            return;
        }

        if (!$prospecto->fecha_firma) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Sin fecha de cita',
                'text' => 'Este prospecto no tiene fecha de firma agendada. Primero debe agendar su cita.',
            ]);
            return;
        }

        // Despachar evento para notificaciones (Email/WhatsApp)
        EntregaFestCitaRecordatorio::dispatch($prospecto);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Recordatorio en cola!',
            'text' => 'Se está enviando el recordatorio por Correo y WhatsApp.',
        ]);
    }

    public function render()
    {
        $usuarios        = User::role(['asesor-backoffice', 'supervisor-backoffice'])->get();
        $usuariosLlamada = User::role(['asesor-atc', 'asesor-backoffice', 'supervisor-backoffice'])->get();

        // 🆕 Filtro por área Legal (sin importar el rol)
        $usuariosLegal = User::where('activo', true)
            ->whereHas('areas', fn($q) => $q->where('area_user.area_id', 3))
            ->orderBy('name')
            ->get();

        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-editar', [
            'usuarios'        => $usuarios,
            'usuariosLlamada' => $usuariosLlamada,
            'usuariosLegal'   => $usuariosLegal,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
