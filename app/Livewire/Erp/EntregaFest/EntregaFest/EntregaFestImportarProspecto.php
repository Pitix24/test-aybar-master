<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Imports\ProspectoEntregaFestImport;
use App\Models\EntregaFest;
use App\Models\ProspectoHistorico; // <-- Agregado en Fase 3
use App\Models\ProspectoEntregaFest; // <-- Agregado en Fase 3
use App\Models\CopropietarioEntregaFest; // <-- Agregado en Fase 3
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EntregaFestImportarProspecto extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $archivo_excel;

    public function mount(EntregaFest $evento)
    {
        $this->evento = $evento;
    }

    public function descargarPlantilla()
    {
        return response()->download(public_path('templates/formato_importar_prospectos_entrega_fest.xlsx'));
    }

    public function importarProspectos()
    {
        if (!$this->archivo_excel) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Advertencia', 'text' => 'Debe seleccionar un archivo Excel.']);
            return;
        }

        $proyectosValidos = collect($this->evento->proyectos)->pluck('id')->toArray();

        try {
            DB::beginTransaction();

            $import = new ProspectoEntregaFestImport($this->evento->id, $proyectosValidos);
            Excel::import($import, $this->archivo_excel->getRealPath());

            DB::commit();
            $this->reset('archivo_excel');

            $text = "Importación completada: Se han registrado {$import->nuevos} prospectos correctamente.";
            $title = '¡Importación Exitosa!';
            $type = 'success';

            if ($import->actualizados > 0) {
                $text = "Se han importado {$import->nuevos} registros nuevos y actualizado {$import->actualizados} existentes.";
                $title = 'Importación con Actualizaciones';
                $type = 'info';
            }

            if (count($import->errores) > 0) {
                $text .= " | Atención: " . count($import->errores) . " filas no se pudieron procesar por errores.";
                $type = 'warning';
            }

            $this->dispatch('alertaLivewire', [
                'type' => $type,
                'title' => $title,
                'text' => $text,
                'showConfirmButton' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[PROSPECTO-IMPORT] : " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function cargarDesdeHistorico()
    {
        // 1. Validación de seguridad
        abort_if(!auth()->user()->can('prospecto-historico.cargar-desde-historico'), 403, 'No tienes permiso para autocargar desde el histórico.');

        $proyectosValidos = collect($this->evento->proyectos)->pluck('id')->toArray();

        if (empty($proyectosValidos)) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Atención', 'text' => 'El evento no tiene proyectos asignados.']);
            return;
        }

        try {
            DB::beginTransaction();

            // 2. Extraer del histórico SOLO los no entregados y que pertenezcan a los proyectos del evento
            $historicos = ProspectoHistorico::whereIn('proyecto_id', $proyectosValidos)
                                ->loteDisponible() // Scope creado en la Fase 1 (lote_entregado = false)
                                ->get();

            $nuevos = 0;
            $omitidos = 0;

            foreach ($historicos as $historico) {
                // 3. Verificar si el prospecto ya existe en ESTE evento (Evitar duplicidad actual)
                $existe = ProspectoEntregaFest::where('entrega_fest_id', $this->evento->id)
                    ->where('prospecto_historico_id', $historico->id) // Usamos la nueva llave foránea
                    ->exists();

                if ($existe) {
                    $omitidos++;
                    continue;
                }

                // ==============================================================
                // 🔥 LA MAGIA DEL APAGADO AUTOMÁTICO:
                // Desactivamos cualquier participación previa de este cliente
                // ==============================================================
                ProspectoEntregaFest::where('prospecto_historico_id', $historico->id)
                    ->update(['activo' => false]);

                // 4. Crear al Titular en el Evento actual
                $prospecto = ProspectoEntregaFest::create([
                    'entrega_fest_id' => $this->evento->id,
                    'proyecto_id' => $historico->proyecto_id,
                    'prospecto_historico_id' => $historico->id,
                    'activo' => true,
                    'user_id' => $historico->user_id,
                    'dni' => $historico->dni,
                    'nombres' => $historico->nombres,
                    'email' => $historico->email,
                    'celular' => $historico->celular,
                    'lote' => $historico->lote,
                    'manzana' => $historico->manzana,
                    'grupo' => $historico->grupo,
                    'estado_cliente_id' => $historico->estado_cliente_id,
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
                    'created_by' => auth()->id(),
                ]);

                // 5. Crear a los Copropietarios (Con protección anti-duplicados por mala data del Excel)
                $copropietarios = $historico->coproPietariosExcel();

                foreach ($copropietarios as $copro) {
                    CopropietarioEntregaFest::updateOrCreate(
                        [
                            // Llave de validación (El mismo DNI no se puede repetir en este Ticket)
                            'prospecto_entrega_fest_id' => $prospecto->id,
                            'dni' => $copro['dni'],
                        ],
                        [
                            // Datos a rellenar o actualizar
                            'nombres' => $copro['nombres'],
                            'email' => $copro['email'],
                            'celular' => $copro['celular'],
                            'created_by' => auth()->id(),
                        ]
                    );
                }
                $nuevos++;
            }

            DB::commit();

            // Notificación al usuario
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Carga Inteligente Completada',
                'text' => "Se registraron {$nuevos} prospectos nuevos. Se omitieron {$omitidos} que ya habían sido agregados anteriormente.",
                'showConfirmButton' => true
            ]);

            // Emitir un evento genérico por si la tabla padre necesita refrescarse
            $this->dispatch('prospectosCargados');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[CARGA-HISTORICO] Error: " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error Crítico', 'text' => 'Ocurrió un error al procesar el histórico. Revisa los logs.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-importar-prospecto');
    }
}
