<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Registrar Prospecto - Entrega Fest')]
class EntregaFestProspectoCrear extends Component
{
    public EntregaFest $evento;

    // Campos del prospecto (Simplificado para registro inicial)
    public $proyecto_id = '', $dni = '', $nombres = '', $email = '', $celular = '';
    public $lote = '', $manzana = '';

    public $proyectos = [];

    protected function rules()
    {
        return [
            'proyecto_id' => 'required|exists:proyectos,id',
            'dni' => 'required|string|max:15',
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'celular' => 'required|string|max:20',
            'lote' => 'nullable|string|max:20',
            'manzana' => 'nullable|string|max:20',
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
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
    }

    public function store()
    {
        $this->authorize('prospecto.crear');

        $lote = strtoupper(trim((string) $this->lote));
        $manzana = strtoupper(trim((string) $this->manzana));

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        // Verificar duplicado de Lote y Manzana en este evento (Lógica Original Intacta)
        if ($lote !== '' || $manzana !== '') {
            $loteManzanaExiste = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
                ->where('proyecto_id', $this->proyecto_id)
                ->where('lote', $lote)
                ->where('manzana', $manzana)
                ->exists();

            if ($loteManzanaExiste) {
                $this->addError('lote', 'Lote ocupado');
                $this->addError('manzana', 'Manzana ocupada');
                $this->dispatch('alertaLivewire', [
                    'type' => 'error',
                    'title' => 'Ubicación Duplicada',
                    'text' => "El Lote {$lote} de la Manzana {$manzana} ya está registrado para este proyecto."
                ]);
                return;
            }
        }

        try {
            DB::beginTransaction();

            // =========================================================
            // 1. ALIMENTAR TABLA MAESTRA (Histórico)
            // =========================================================
            $historico = \App\Models\ProspectoHistorico::updateOrCreate(
                [
                    'proyecto_id' => $this->proyecto_id,
                    'dni'         => $this->dni,
                    'lote'        => $lote !== '' ? $lote : null,
                    'manzana'     => $manzana !== '' ? $manzana : null,
                ],
                [
                    'nombres'    => trim($this->nombres),
                    'email'      => trim($this->email),
                    'celular'    => trim($this->celular),
                    'user_id'    => Auth::id(),
                    // 'updated_by' y 'created_by' ya se manejan solos en el Modelo
                ]
            );

            // Validar si el cliente ya está en el evento (Extra seguridad por DNI)
            $existeEnEvento = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
                ->where('prospecto_historico_id', $historico->id)
                ->exists();

            if ($existeEnEvento) {
                DB::rollBack();
                $this->dispatch('alertaLivewire', [
                    'type' => 'error',
                    'title' => 'Duplicado',
                    'text' => 'Este cliente ya está registrado en la lista de invitados de este evento.'
                ]);
                return;
            }

            // =========================================================
            // 2. APAGAR HISTORIALES PASADOS
            // =========================================================
            ProspectoEntregaFest::where('prospecto_historico_id', $historico->id)
                ->update(['activo' => false]);

            // =========================================================
            // 3. CREAR EL TICKET EN EL EVENTO ACTUAL
            // =========================================================
            $prospecto = ProspectoEntregaFest::create([
                'entrega_fest_id'        => $this->evento->id,
                'prospecto_historico_id' => $historico->id, // Conexión
                'activo'                 => true,           // Nace activo
                'proyecto_id'            => $this->proyecto_id,
                'user_id'                => Auth::id(),
                'dni'                    => $this->dni,
                'nombres'                => trim($this->nombres),
                'email'                  => trim($this->email),
                'celular'                => trim($this->celular),
                'lote'                   => $lote !== '' ? $lote : null,
                'manzana'                => $manzana !== '' ? $manzana : null,

                // 🆕 HEREDAR PROGRESO DEL TRÁMITE DESDE EL HISTÓRICO
                'reubicado_proyecto_id' => $historico->reubicado_proyecto_id,
                'reubicado_lote' => $historico->reubicado_lote,
                'reubicado_manzana' => $historico->reubicado_manzana,
                'estado_backoffice' => $historico->estado_backoffice ?? 'PENDIENTE',
                'gestor_backoffice_id' => $historico->gestor_backoffice_id,
                'gestor_fecha_asignacion' => $historico->gestor_fecha_asignacion,
                'estado_gestor_backoffice' => $historico->estado_gestor_backoffice ?? 'PENDIENTE',
                'observacion_gestor_backoffice' => $historico->observacion_gestor_backoffice,
                'fecha_culminacion_eecc' => $historico->fecha_culminacion_eecc,
                'link_carpeta_eecc' => $historico->link_carpeta_eecc,
                'link_eecc_firmado' => $historico->link_eecc_firmado,
                'validador_backoffice_id' => $historico->validador_backoffice_id,
                'fecha_validacion_eecc' => $historico->fecha_validacion_eecc,
                'responsable_llamada_id' => $historico->responsable_llamada_id,
                'responsable_llamada_fecha_asignacion' => $historico->responsable_llamada_fecha_asignacion,
                'gestor_legal_id' => $historico->gestor_legal_id,
                'legal_fecha_asignacion' => $historico->legal_fecha_asignacion,
                'observacion_gestor_legal' => $historico->observacion_gestor_legal,
                'validador_legal_id' => $historico->validador_legal_id,
                'fecha_firma_presencial' => $historico->fecha_firma_presencial,
                'fecha_validacion_firma' => $historico->fecha_validacion_firma,
                'estado_contrato_preeliminar_emitido' => $historico->estado_contrato_preeliminar_emitido ?? 'PENDIENTE',
                'estado_firma_contrato_firmado' => $historico->estado_firma_contrato_firmado ?? 'PENDIENTE',
                'fecha_firma' => $historico->fecha_firma,
                'fecha_generacion_contrato' => $historico->fecha_generacion_contrato,
            ]);

            Log::channel('entrega-fest')->info('[PROSPECTO CREAR] Registro exitoso', [
                'usuario_id' => Auth::id(),
                'prospecto_id' => $prospecto->id,
                'evento_id' => $this->evento->id,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Prospecto ' . $this->nombres . ' registrado correctamente e indexado al historial.'
            ]);

            return redirect()->route('erp.entrega-fest.prospecto.todo', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[PROSPECTO CREAR] Error al registrar: " . $e->getMessage(), [
                'usuario_id' => Auth::id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar el prospecto.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
