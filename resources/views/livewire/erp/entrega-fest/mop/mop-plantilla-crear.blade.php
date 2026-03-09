<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Nueva Plantilla MOP
            <span>Biblioteca Global</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop-plantilla.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i></a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>
    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-file-lines"></i> Datos de la Plantilla</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Rol / Cargo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="rol_nombre" class="@error('rol_nombre') input-error @enderror"
                            placeholder="Ej: Coordinador de Piso">
                        @error('rol_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Fase <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="fase" class="@error('fase') select-error @enderror">
                            <option value="ANTES">Antes del Evento</option>
                            <option value="DURANTE">Durante el Evento</option>
                            <option value="CIERRE">Cierre</option>
                        </select>
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Prioridad <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <input type="number" wire:model="prioridad" min="1"
                            class="@error('prioridad') input-error @enderror">
                        <p class="leyenda">1 = mayor prioridad</p>
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Instruccion <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="instruccion" rows="4" class="@error('instruccion') input-error @enderror"
                        placeholder="Describe la tarea o instruccion..."></textarea>
                    @error('instruccion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store"><i class="fa-solid fa-save"></i> Guardar</span>
                        <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.mop-plantilla.todo') }}" class="g_boton cancelar"><i
                            class="fa-solid fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>

        <div class="g_columna_4 formulario">
            <div class="g_panel">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h4 class="g_panel_titulo" style="margin: 0;"><i class="fa-solid fa-file-excel"></i> Importar
                        Excel</h4>
                    <button wire:click="descargarPlantilla" class="g_boton info small" title="Descargar formato Excel">
                        <i class="fa-solid fa-download"></i> Plantilla
                    </button>
                </div>
                <p class="leyenda" style="margin-bottom: 15px;">Carga múltiples plantillas MOP de forma masiva.</p>

                <div class="g_margin_bottom_10">
                    <input type="file" wire:model="archivo_excel" id="archivo_excel"
                        class="@error('archivo_excel') input-error @enderror">
                    @error('archivo_excel') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button wire:click="importarExcel" class="g_boton dark" wire:loading.attr="disabled"
                        wire:target="archivo_excel, importarExcel">
                        <span wire:loading.remove wire:target="importarExcel">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Procesar Excel
                        </span>
                        <span wire:loading wire:target="importarExcel">
                            <i class="fa-solid fa-spinner fa-spin"></i> Importando...
                        </span>
                    </button>
                </div>

                <div wire:loading wire:target="archivo_excel" class="g_margin_top_10">
                    <p style="font-size: 0.8em; color: var(--color-primary);"><i
                            class="fa-solid fa-spinner fa-spin"></i> Cargando archivo...</p>
                </div>
            </div>
        </div>
    </div>
</div>