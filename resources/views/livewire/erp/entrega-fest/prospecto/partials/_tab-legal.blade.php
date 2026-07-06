<div class="g_gap_pagina">
    {{-- ============ FORMULARIO 1 — GESTOR LEGAL ============ --}}
    <form wire:submit.prevent="updateLegal" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_4">
                <label>Gestor Legal Asignado</label>
                <select wire:model="gestor_legal_id">
                    <option value="">Sin asignar</option>
                    @foreach ($usuariosLegal as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
                @if ($legal_fecha_asignacion)
                    <p class="leyenda" style="margin-top: 5px;">
                        <i class="fa-solid fa-clock"></i> Asignado el:
                        {{ date('d/m/Y H:i', strtotime($legal_fecha_asignacion)) }}
                    </p>
                @endif
            </div>

            <div class="g_margin_bottom_10 g_columna_4">
                <label>Estado Contrato Preliminar <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model="estado_contrato_preeliminar_emitido">
                    @foreach (\App\Models\ProspectoEntregaFest::ESTADO_CONTRATO_PRELIMINAR as $valor => $info)
                        <option value="{{ $valor }}">{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="g_margin_bottom_10 g_columna_4">
                <label>
                    Fecha Generación Contrato
                </label>
                <input type="datetime-local"
                    value="{{ $prospecto->fecha_generacion_contrato}}"
                    disabled readonly>
                @if (!$prospecto->fecha_generacion_contrato)
                    <p class="leyenda" style="margin-top: 5px; font-size: 0.75rem;">
                        <i class="fa-solid fa-clock"></i> Se usará la fecha/hora actual al confirmar el contrato.
                    </p>
                @endif
            </div>
        </div>

        <div class="g_fila">
            <div class="g_columna_12 g_margin_bottom_10">
                <label>Adjuntar Contrato Preliminar (PDF) <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>

                @if($prospecto->hasMedia('contrato-preliminar'))
                <div class="g_panel"
                    style="background-color: #f0fdf4; border: 1px solid #bbf7d0; margin-bottom: 10px; padding: 10px;">
                    <div class="g_fila g_flex_centro_vertical">
                        <div class="g_columna_8">
                            <p class="g_texto_pequeno" style="color: #166534; margin: 0;">
                                <i class="fa-solid fa-circle-check"></i> <b>Contrato ya cargado:</b>
                                <a href="{{ $prospecto->getFirstMediaUrl('contrato-preliminar') }}" target="_blank"
                                    class="g_link verde" style="font-weight: 600;">
                                    <i class="fa-solid fa-file-pdf"></i> Ver documento actual
                                </a>
                            </p>
                        </div>
                        <div class="g_columna_4 g_texto_derecha">
                            <span class="g_badge info">Para editar, sube un nuevo archivo abajo</span>
                            @can('contrato-preliminar.eliminar')
                            <button type="button" wire:click="solicitarEliminarContratoPreliminar"
                                wire:loading.attr="disabled"
                                wire:target="solicitarEliminarContratoPreliminar,eliminarContratoPreliminarOn"
                                class="g_boton peligro g_margin_top_10" title="Eliminar contrato preliminar actual">
                                <span wire:loading.remove
                                    wire:target="solicitarEliminarContratoPreliminar,eliminarContratoPreliminarOn">
                                    <i class="fa-solid fa-trash"></i> Eliminar contrato
                                </span>
                                <span wire:loading
                                    wire:target="solicitarEliminarContratoPreliminar,eliminarContratoPreliminarOn">
                                    Procesando... <i class="fa-solid fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endif

                <div class="g_input_file_container">
                    <input type="file" wire:model="archivo_contrato_preeliminar" id="archivo_contrato_preeliminar"
                        accept="application/pdf" class="g_input">
                    <div wire:loading wire:target="archivo_contrato_preeliminar" class="g_texto_small azul">
                        <i class="fa-solid fa-spinner fa-spin"></i> Subiendo archivo temporal...
                    </div>
                </div>
                @error('archivo_contrato_preeliminar') <span class="g_texto_error">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_12">
                <label>Observación Gestor Legal</label>
                <textarea wire:model="observacion_gestor_legal" rows="4"
                    placeholder="Escriba aquí las observaciones del gestor legal..."></textarea>
            </div>
        </div>

        <div class="g_tab_form_buttons">
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Guardar Seguimiento Legal
            </button>
        </div>
    </form>

    {{-- ============ FORMULARIO 2 — VALIDADOR LEGAL (FIRMA) ============ --}}
    <form wire:submit.prevent="updateLegalSupervisor" class="formulario">

        {{-- Aviso de prerequisitos --}}
        @if (!$prospecto->gestor_legal_id || $prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME')
            <div style="background: #fef3c7; border: 1px dashed #f59e0b; border-radius: 8px;
                        padding: 12px; margin: 0 0 15px 0; color: #92400e; font-size: 0.9rem;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <strong>Para confirmar la firma necesitas:</strong>
                <ul style="margin: 5px 0 0 20px;">
                    @if (!$prospecto->gestor_legal_id)
                        <li>Asignar un Gestor Legal en el formulario superior.</li>
                    @endif
                    @if ($prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME')
                        <li>Confirmar el Contrato Preliminar (estado debe ser <strong>CONFORME</strong>).</li>
                    @endif
                </ul>
            </div>
        @endif

        {{-- Fila 1: Estado + Fecha generación --}}
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>
                    Fecha Cita Solicitada por el Cliente
                </label>
                <input type="text"
                       value="{{ $prospecto->fecha_firma ? date('d/m/Y H:i', strtotime($prospecto->fecha_firma)) : 'Aún no agendada' }}"
                       disabled readonly>
            </div>
        </div>

        {{-- Fila 2: Las 3 fechas claramente separadas --}}
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Estado Firma Contrato <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model="estado_firma_contrato_firmado">
                    @foreach (\App\Models\ProspectoEntregaFest::ESTADO_FIRMA as $valor => $info)
                        <option value="{{ $valor }}">{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="g_margin_bottom_10 g_columna_6">
                <label>
                    Fecha Firma Presencial
                    <span class="obligatorio" style="font-size: 0.75rem;"> *</span>
                </label>
                <input type="datetime-local" wire:model="fecha_firma_presencial">
                <p class="leyenda" style="margin-top: 5px; font-size: 0.75rem;">
                        <i class="fa-solid fa-clock"></i> Fecha de firma presencial.
                </p>
            </div>
        </div>

        {{-- Fila 3: Validador (auditoría) --}}
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Validador Legal</label>
                <input type="text"
                       value="{{ $prospecto->validadorLegal?->name ?? auth()->user()->name . ' (sin registrar aún)' }}"
                       disabled readonly>
            </div>

            <div class="g_margin_bottom_10 g_columna_6">
                <label>
                    Fecha de Validación
                </label>
                <input type="datetime-local"
                       value="{{ $fecha_validacion_firma }}"
                       disabled readonly>
                @if (!$fecha_validacion_firma)
                    <p class="leyenda" style="margin-top: 5px; font-size: 0.75rem;">
                        <i class="fa-solid fa-clock"></i> Se usará la fecha/hora actual al validar.
                    </p>
                @endif
            </div>
        </div>

        <div class="g_tab_form_buttons">
            @can('prospecto.editar')
                <button type="submit" class="g_boton guardar">
                    <i class="fa-solid fa-check-circle"></i> Confirmar
                </button>
            @endcan
        </div>
    </form>

</div>
