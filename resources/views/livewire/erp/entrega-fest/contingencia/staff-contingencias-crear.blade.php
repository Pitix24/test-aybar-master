<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Añadir Contingencia
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.contingencia.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-shield-virus"></i> Escenario de Riesgo</h4>

                <div class="g_margin_bottom_10">
                    <label>Nombre del Escenario / Riesgo <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="escenario" placeholder="Ej: Falla de energía eléctrica">
                    @error('escenario') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10">
                    <label>Acción de Respuesta Inmediata <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="accion" rows="6"
                        placeholder="¿Qué debe hacer el staff exactamente?"></textarea>
                    @error('accion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store"><i class="fa-solid fa-save"></i> Guardar
                            Plan</span>
                        <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.contingencia.todo', $evento->id) }}" class="g_boton cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Ayuda</h4>
                <p class="g_panel_parrafo">Los planes de contingencia previenen el caos ante situaciones críticas:</p>
                <ul class="g_lista_check">
                    <li><i class="fa-solid fa-check"></i> Falla técnica.</li>
                    <li><i class="fa-solid fa-check"></i> Situación de salud.</li>
                    <li><i class="fa-solid fa-check"></i> Eventos climáticos o estructurales.</li>
                </ul>
            </div>
        </div>
    </div>

</div>