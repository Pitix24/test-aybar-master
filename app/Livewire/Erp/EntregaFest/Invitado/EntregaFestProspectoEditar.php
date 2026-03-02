<?php

namespace App\Livewire\Erp\EntregaFest\Invitado;

use App\Models\CopropietarioEntregaFest;
use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EntregaFest\FirmaConfirmacionMail;
use App\Mail\EntregaFest\FirmaLinkMail;
use App\Mail\EntregaFest\AsistenciaLinkMail;
use App\Mail\EntregaFest\AsistenciaLinkCopropietarioMail;
use App\Services\WhatsappService;
use App\Models\Cliente;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use Illuminate\Validation\ValidationException;
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
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '', $estado = '', $observacion = '';
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
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'observacion' => 'nullable|string',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',

            // BackOffice
            'grupo' => 'required|in:A,B,C,D',
            'gestor_backoffice_id' => 'nullable|exists:users,id',
            'fecha_culminacion_eecc' => 'nullable|date',
            'link_carpeta_eecc' => 'nullable|string|max:255',
            'link_eecc_firmado' => 'nullable|string|max:255',
            'validador_backoffice_id' => 'nullable|exists:users,id',
            'fecha_validacion_eecc' => 'nullable|date',
            'estado_backoffice' => 'required|in:pendiente,observado,aprobado,rechazado',

            // Legal
            'estado_contrato_preeliminar_emitido' => 'required|in:pendiente,observado,aprobado,rechazado',
            'estado_firma_contrato_firmado' => 'required|in:pendiente,observado,aprobado,rechazado',
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
            'estado' => 'estado del prospecto',
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
        $this->estado = $this->prospecto->estado;
        $this->observacion = $this->prospecto->observacion;
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
            'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'observacion' => 'nullable|string',
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
            'estado' => $this->estado,
            'observacion' => $this->observacion,
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
            'validador_backoffice_id' => 'nullable|exists:users,id',
            'fecha_validacion_eecc' => 'nullable|date',
            'estado_backoffice' => 'required|in:pendiente,observado,aprobado,rechazado',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'grupo' => $this->grupo,
            'gestor_backoffice_id' => $this->gestor_backoffice_id ?: null,
            'fecha_culminacion_eecc' => $this->fecha_culminacion_eecc,
            'link_carpeta_eecc' => $this->link_carpeta_eecc,
            'link_eecc_firmado' => $this->link_eecc_firmado,
            'validador_backoffice_id' => $this->validador_backoffice_id ?: null,
            'fecha_validacion_eecc' => $this->fecha_validacion_eecc,
            'estado_backoffice' => $this->estado_backoffice,
        ], 'PROSPECTO EDITAR - BACKOFFICE');

        // Si se acaba de aprobar, enviamos invitación
        if ($this->estado_backoffice === 'aprobado') {
            $this->dispararInvitaciones(app(WhatsappService::class));
        }
    }

    private function dispararInvitaciones(WhatsappService $whatsapp)
    {
        $prospecto = $this->prospecto->fresh(['invitado', 'copropietarios.invitado']);
        $enviadosEmail = 0;
        $enviadosWsp = 0;

        // 1. ENVIAR AL TITULAR (Si no tiene invitación y tiene datos)
        if (!$prospecto->invitado) {
            // Email
            if ($prospecto->email) {
                try {
                    Mail::to($prospecto->email)->send(new AsistenciaLinkMail($prospecto));
                    $enviadosEmail++;
                } catch (\Exception $e) {
                    Log::error("[ENTREGA-FEST-AUTO] Error correo titular {$prospecto->email}: " . $e->getMessage());
                }
            }

            // WhatsApp
            if ($prospecto->celular) {
                $formatearCelular = function (string $raw): string {
                    $cel = preg_replace('/\D/', '', $raw);
                    return strlen($cel) === 9 ? '51' . $cel : $cel;
                };

                $link = route('public.entrega-fest.asistencia', [$this->evento->slug, $prospecto->id]);
                $mensaje = "Hola *{$prospecto->nombres}*, ya tenemos tu evaluación lista para el evento *{$this->evento->nombre}*. Confirma tu asistencia aquí: $link";
                $celular = $formatearCelular($prospecto->celular);

                $response = $whatsapp->sendText($celular, $mensaje);
                if ($response) {
                    $enviadosWsp++;
                    $this->registrarMensajeWsp($celular, $prospecto->nombres, $prospecto->dni, $mensaje, $response['messages'][0]['id'] ?? 'AUTO_' . uniqid());
                }
            }
        }

        // 2. ENVIAR A COPROPIETARIOS
        foreach ($prospecto->copropietarios as $cop) {
            if (!$cop->invitado) {
                // Email
                if ($cop->email) {
                    try {
                        Mail::to($cop->email)->send(new AsistenciaLinkCopropietarioMail($cop));
                        $enviadosEmail++;
                    } catch (\Exception $e) {
                        Log::error("[ENTREGA-FEST-AUTO] Error correo copropietario {$cop->email}: " . $e->getMessage());
                    }
                }

                // WhatsApp
                if ($cop->celular) {
                    $formatearCelular = function (string $raw): string {
                        $cel = preg_replace('/\D/', '', $raw);
                        return strlen($cel) === 9 ? '51' . $cel : $cel;
                    };

                    $link = route('public.entrega-fest.asistencia.copropietario', [$this->evento->slug, $cop->id]);
                    $mensaje = "Hola *{$cop->nombres}*, ya tienes evaluación lista para el evento *{$this->evento->nombre}*. Confirma tu asistencia aquí: $link";
                    $celular = $formatearCelular($cop->celular);

                    $response = $whatsapp->sendText($celular, $mensaje);
                    if ($response) {
                        $enviadosWsp++;
                        $this->registrarMensajeWsp($celular, $cop->nombres, $cop->dni, $mensaje, $response['messages'][0]['id'] ?? 'AUTO_COP_' . uniqid());
                    }
                }
            }
        }

        if ($enviadosEmail > 0 || $enviadosWsp > 0) {
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Invitaciones Enviadas',
                'text' => "Se enviaron $enviadosEmail correos y $enviadosWsp mensajes de WhatsApp."
            ]);
        }
    }

    private function registrarMensajeWsp($celular, $nombre, $dni, $mensaje, $waMessageId)
    {
        $cliente = Cliente::where('dni', $dni)->first();
        $contacto = WhatsappContacto::updateOrCreate(
            ['wa_id' => $celular],
            ['nombre_wa' => $nombre, 'numero_celular' => $celular, 'cliente_id' => $cliente?->id]
        );
        $conversacion = WhatsappConversacion::firstOrCreate(
            ['contacto_id' => $contacto->id],
            ['cliente_id' => $cliente?->id, 'estado' => 'asignado', 'departamento_destino' => 'backoffice', 'agente_id' => auth()->id()]
        );
        $conversacion->update(['last_message_at' => now()]);
        WhatsappMensaje::create([
            'conversacion_id' => $conversacion->id,
            'direccion' => 'saliente',
            'tipo' => 'texto',
            'contenido' => $mensaje,
            'wa_message_id' => $waMessageId,
            'estado' => 'enviado',
        ]);
    }

    public function updateLegal()
    {
        $rules = [
            'estado_contrato_preeliminar_emitido' => 'required|in:pendiente,observado,aprobado,rechazado',
            'estado_firma_contrato_firmado' => 'required|in:pendiente,observado,aprobado,rechazado',
            'fecha_firma' => 'nullable|date',
            'fecha_generacion_contrato' => 'nullable|date',
        ];

        $this->validate($rules);

        $this->handleUpdate([
            'estado_contrato_preeliminar_emitido' => $this->estado_contrato_preeliminar_emitido,
            'estado_firma_contrato_firmado' => $this->estado_firma_contrato_firmado,
            'fecha_firma' => $this->fecha_firma,
            'fecha_generacion_contrato' => $this->fecha_generacion_contrato,
        ], 'PROSPECTO EDITAR - LEGAL');

        // Si se aprueba legal, disparamos agendamiento de firma
        if ($this->estado_contrato_preeliminar_emitido === 'aprobado') {
            $this->dispararInvitacionesFirma(app(WhatsappService::class));
        }
    }

    private function dispararInvitacionesFirma(WhatsappService $whatsapp)
    {
        $prospecto = $this->prospecto->fresh();

        // Según lógica de negocio: solo si no tiene fecha agendada aún
        if ($prospecto->fecha_firma)
            return;

        $enviadoEmail = false;
        $enviadoWsp = false;

        // Email
        if ($prospecto->email) {
            try {
                Mail::to($prospecto->email)->send(new FirmaLinkMail($prospecto));
                $enviadoEmail = true;
            } catch (\Exception $e) {
                Log::error('[FIRMA CORREO AUTO] Error a ' . $prospecto->email . ': ' . $e->getMessage());
            }
        }

        // WhatsApp
        if ($prospecto->celular) {
            $formatearCelular = function (string $raw): string {
                $cel = preg_replace('/\D/', '', $raw);
                return strlen($cel) === 9 ? '51' . $cel : $cel;
            };

            $link = route('public.entrega-fest.firma', [$this->evento->slug, $prospecto->id]);
            $mensaje = "Hola *{$prospecto->nombres}*, tu contrato preliminar para el evento *{$this->evento->nombre}* está aprobado 🎉. Agenda aquí tu cita de firma: $link";
            $celular = $formatearCelular($prospecto->celular);

            $response = $whatsapp->sendText($celular, $mensaje);
            if ($response) {
                $enviadoWsp = true;
                $this->registrarMensajeWsp($celular, $prospecto->nombres, $prospecto->dni, $mensaje, $response['messages'][0]['id'] ?? 'AUTO_FIRMA_' . uniqid());
            }
        }

        if ($enviadoEmail || $enviadoWsp) {
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Link de Firma Enviado',
                'text' => "Se envió el agendamiento al prospecto correctamente."
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // RECORDATORIO DE FIRMA
    // ──────────────────────────────────────────────────────────────────

    public function enviarCorreoFirmaRecordatorio()
    {
        // Recargar el prospecto actual desde BD con relaciones necesarias
        $prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto'])
            ->find($this->prospecto->id);

        // Validar condiciones
        if ($prospecto->estado_backoffice !== 'aprobado') {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No permitido',
                'text' => 'El prospecto no tiene el BackOffice aprobado.',
            ]);
            return;
        }

        if ($prospecto->estado_contrato_preeliminar_emitido !== 'aprobado') {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'No permitido',
                'text' => 'El contrato preliminar aún no está aprobado.',
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

        if (!$prospecto->email) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Sin email',
                'text' => 'Este prospecto no tiene email registrado.',
            ]);
            return;
        }

        try {
            Mail::to($prospecto->email)->send(new FirmaConfirmacionMail($prospecto));

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Correo enviado!',
                'text' => 'Se envió el recordatorio de firma a ' . $prospecto->email . '.',
            ]);
        } catch (\Exception $e) {
            Log::error('[FIRMA RECORDATORIO] Error: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error al enviar',
                'text' => 'No se pudo enviar el correo. Revisa los logs.',
            ]);
        }
    }

    public function render()
    {
        $usuarios = User::orderBy('name')->get();
        return view('livewire.erp.entrega-fest.invitado.entrega-fest-prospecto-editar', [
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
