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

        <div class="g_tab_form_buttons">
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Guardar Seguimiento Legal
            </button>
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
            <button type="submit" class="g_boton guardar">
                <i class="fa-solid fa-save"></i> Confirmar
            </button>

            @if($fecha_firma)
                <button type="button" wire:click="enviarCorreoFirmaRecordatorio" wire:loading.attr="disabled"
                    wire:target="enviarCorreoFirmaRecordatorio" class="g_boton info"
                    title="Enviar recordatorio de cita de firma al prospecto">
                    <span wire:loading.remove wire:target="enviarCorreoFirmaRecordatorio">
                        <i class="fa-solid fa-envelope-circle-check"></i> Recordatorio de Firma
                    </span>
                    <span wire:loading wire:target="enviarCorreoFirmaRecordatorio">
                        Enviando... <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                </button>
            @endif
        </div>
    </form>
</div>