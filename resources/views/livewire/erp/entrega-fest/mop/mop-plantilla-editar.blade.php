<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Editar Plantilla MOP
            <span>Biblioteca Global</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop-plantilla.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i></a>

            <a href="{{ route('erp.entrega-fest.mop-plantilla.crear') }}" class="g_boton primary">
                Crear <i class="fa-solid fa-square-plus"></i></a>

            <button type="button" class="g_boton danger"
                onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarPlantillaOn', titulo: 'Eliminar Plantilla', texto: 'Esta accion no se puede deshacer.' })">
                Eliminar <i class="fa-solid fa-trash"></i>
            </button>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>
    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="update" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-pencil"></i> Editar Plantilla #{{ $plantilla->id }}
                </h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Rol / Cargo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="rol_nombre" class="@error('rol_nombre') input-error @enderror">
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
                        <label>Prioridad</label>
                        <input type="number" wire:model="prioridad" min="1">
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Instruccion <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="instruccion" rows="4"
                        class="@error('instruccion') input-error @enderror"></textarea>
                    @error('instruccion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i>
                            Actualizar</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.mop-plantilla.todo') }}" class="g_boton cancelar"><i
                            class="fa-solid fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>