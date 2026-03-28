<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Events\EntregaFest\EntregaFestAsistenciaInvitacion;
use App\Events\EntregaFest\EntregaFestContratoPreliminar;
use App\Models\CopropietarioEntregaFest;
use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\ProspectoLegalConforme;
use App\Events\EntregaFestFirmaRecordatorio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Evaluar Prospecto - Entrega Fest')]
class EntregaFestProspectoEditar extends Component
{
    public EntregaFest $evento;
    public ProspectoEntregaFest $prospecto;

    // Campos del prospecto
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '';
    public $lote = '', $manzana = '';

    // BackOffice
    public $grupo, $gestor_backoffice_id = '', $fecha_culminacion_eecc, $link_carpeta_eecc, $link_eecc_firmado;
    public $validador_backoffice_id = '', $fecha_validacion_eecc, $estado_backoffice;

    // Legal
    public $estado_contrato_preeliminar_emitido, $estado_firma_contrato_firmado;
    public $fecha_firma, $fecha_generacion_contrato;

    public $proyectos = [];

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
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',

            // BackOffice — Asesor
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',

            // BackOffice — Supervisor
            'validador_backoffice_id' => 'nullable|exists:users,id',
            'fecha_validacion_eecc' => 'nullable|date',
            'estado_backoffice' => 'required|in:PENDIENTE,BANCARIZAR,PENALIDAD,OBSERVADO,CONFORME',

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

        // BackOffice
        $this->grupo = $this->prospecto->grupo;
        $this->gestor_backoffice_id = $this->prospecto->gestor_backoffice_id;
        $this->fecha_culminacion_eecc = $this->prospecto->fecha_culminacion_eecc
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_culminacion_eecc)) : null;
        $this->link_carpeta_eecc = $this->prospecto->link_carpeta_eecc;
        $this->link_eecc_firmado = $this->prospecto->link_eecc_firmado;
        $this->validador_backoffice_id = $this->prospecto->validador_backoffice_id;
        $this->fecha_validacion_eecc = $this->prospecto->fecha_validacion_eecc
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_validacion_eecc)) : null;
        $this->estado_backoffice = $this->prospecto->estado_backoffice;

        // Legal
        $this->estado_contrato_preeliminar_emitido = $this->prospecto->estado_contrato_preeliminar_emitido;
        $this->estado_firma_contrato_firmado = $this->prospecto->estado_firma_contrato_firmado;
        $this->fecha_firma = $this->prospecto->fecha_firma
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_firma)) : null;
        $this->fecha_generacion_contrato = $this->prospecto->fecha_generacion_contrato
            ? date('Y-m-d\TH:i', strtotime($this->prospecto->fecha_generacion_contrato)) : null;

        $this->proyectos = $this->evento->proyectos;

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

    private function handleUpdate(array $data, string $logContext)
    {
        $this->authorize('prospecto-entrega-fest.editar');

        try {
            DB::beginTransaction();
            $this->prospecto->update($data);
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Información actualizada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[$logContext] Error: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
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
        ], 'PROSPECTO EDITAR - BASICO');
    }

    public function updateBackoffice()
    {
        $rules = [
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'grupo' => $this->grupo,
            'gestor_backoffice_id' => $this->gestor_backoffice_id ?: null,
            'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
            'link_carpeta_eecc' => $this->link_carpeta_eecc,
            'link_eecc_firmado' => $this->link_eecc_firmado,
        ], 'PROSPECTO EDITAR - BACKOFFICE');
    }

    public function updateBackofficeSupervisor()
    {
        // Auto-asignar si están vacíos al momento de validar
        $this->validador_backoffice_id = auth()->id();

        $this->fecha_validacion_eecc = now()->format('Y-m-d\TH:i');

        $rules = [
            'validador_backoffice_id' => 'required|exists:users,id',
            'fecha_validacion_eecc' => 'required|date',
            'estado_backoffice' => 'required|in:PENDIENTE,BANCARIZAR,PENALIDAD,OBSERVADO,CONFORME',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'validador_backoffice_id' => $this->validador_backoffice_id,
            'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
            'estado_backoffice' => $this->estado_backoffice,
        ], 'PROSPECTO EDITAR - BACKOFFICE');

        // Si se acaba de aprobar (CONFORME), disparamos el evento de invitaciones
        if ($this->estado_backoffice === 'CONFORME') {
            EntregaFestAsistenciaInvitacion::dispatch($this->prospecto);
        }
    }

    public function updateLegal()
    {
        $rules = [
            'estado_contrato_preeliminar_emitido' => 'required|in:PENDIENTE,GENERADO,OBSERVADO,CONFORME',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
        ], 'PROSPECTO EDITAR - LEGAL');

        // Si se aprueba legal (CONFORME), disparamos el evento de firma
        if ($this->estado_contrato_preeliminar_emitido === 'CONFORME') {
            EntregaFestContratoPreliminar::dispatch($this->prospecto);
        }
    }

    public function updateLegalSupervisor()
    {
        $rules = [
            'estado_firma_contrato_firmado' => 'required|in:PENDIENTE,FIRMADO',
            'fecha_generacion_contrato' => 'nullable|date',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
            'fecha_generacion_contrato' => $this->fecha_generacion_contrato,
        ], 'PROSPECTO EDITAR - LEGAL SUPERVISOR');
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
        EntregaFestFirmaRecordatorio::dispatch($prospecto);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Recordatorio en cola!',
            'text' => 'Se está enviando el recordatorio por Correo y WhatsApp.',
        ]);
    }

    public function render()
    {
        $usuarios = User::role(['asesor-backoffice', 'supervisor-backoffice'])->get();
        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-editar', [
            'usuarios' => $usuarios,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
