<div class="g_gap_pagina">
    <form wire:submit.prevent="updateLegal" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Contrato Preliminar <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model="estado_contrato_preeliminar_emitido">
                    @foreach (\App\Models\ProspectoEntregaFest::ESTADO_CONTRATO_PRELIMINAR as $valor => $info)
                    <option value="{{ $valor }}">{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Fecha de Firma</label>
                <input type="datetime-local" value="{{ $fecha_firma }}" disabled>
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

        <div class="g_tab_form_buttons">
            @can('prospecto.editar')
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Guardar Seguimiento Legal
            </button>
            @endcan
        </div>
    </form>

    <form wire:submit.prevent="updateLegalSupervisor" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Firma presencial de Contrato <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model="estado_firma_contrato_firmado">
                    @foreach (\App\Models\ProspectoEntregaFest::ESTADO_FIRMA as $valor => $info)
                    <option value="{{ $valor }}">{{ $info['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="g_margin_bottom_10 g_columna_6">
                <label>Generación de Contrato</label>
                <input type="datetime-local" wire:model="fecha_generacion_contrato">
            </div>
        </div>

        <div class="g_tab_form_buttons">
            @can('prospecto.editar')
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Confirmar
            </button>
            @endcan

            @if($fecha_firma && $estado_firma_contrato_firmado === 'PENDIENTE')
            @can('prospecto.editar')
            <button type="button" wire:click="enviarRecordatorioFirma" wire:loading.attr="disabled"
                wire:target="enviarRecordatorioFirma" class="g_boton info"
                title="Enviar recordatorio de cita de firma al prospecto">
                <span wire:loading.remove wire:target="enviarRecordatorioFirma">
                    <i class="fa-solid fa-envelope-circle-check"></i> Recordatorio de Firma
                </span>
                <span wire:loading wire:target="enviarRecordatorioFirma">
                    Enviando... <i class="fa-solid fa-spinner fa-spin"></i>
                </span>
            </button>
            @endcan
            @endif
        </div>
    </form>
</div>