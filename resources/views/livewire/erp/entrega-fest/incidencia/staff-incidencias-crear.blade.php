<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Reportar Incidencia
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.incidencia.todo', $evento->id) }}" class="g_boton light">
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

    {{-- FORMULARIO DE REPORTE --}}
    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-triangle-exclamation"></i> Nueva Incidencia</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Tipo de Problema</label>
                        <select wire:model="tipo">
                            <option value="Logística">Logística</option>
                            <option value="Seguridad">Seguridad</option>
                            <option value="Técnico">Técnico</option>
                            <option value="Salud">Salud</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Prioridad</label>
                        <select wire:model="prioridad">
                            <option value="BAJA">Baja</option>
                            <option value="MEDIA">Media</option>
                            <option value="ALTA">Alta</option>
                        </select>
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripción de los hechos <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="descripcion" rows="3" placeholder="¿Qué pasó? Sea específico..."></textarea>
                    @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Ubicación exacta</label>
                        <input type="text" wire:model="ubicacion" placeholder="Ej: Puerta 2, Detrás del escenario">
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Evidencia fotográfica</label>
                        <input type="file" wire:model="fotos" multiple>
                        <div wire:loading wire:target="fotos" class="g_inferior">Subiendo archivo...</div>
                        @error('fotos.*') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton danger" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store"><i class="fa-solid fa-paper-plane"></i> Enviar
                            Reporte</span>
                        <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i>
                            Enviando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.incidencia.todo', $evento->id) }}" class="g_boton cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Recomendaciones</h4>
                <p class="g_panel_parrafo">Reporte incidentes que afecten el normal desarrollo del evento:</p>
                <ul class="g_lista_check">
                    <li><i class="fa-solid fa-check"></i> Sea claro en la descripción.</li>
                    <li><i class="fa-solid fa-check"></i> Si puede, tome una foto del problema.</li>
                    <li><i class="fa-solid fa-check"></i> Especifique la ubicación exacta.</li>
                </ul>
            </div>
        </div>
    </div>

</div>