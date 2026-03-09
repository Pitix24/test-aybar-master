<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Editar Protocolo
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.protocolo.todo', $evento->id) }}" class="g_boton light">
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
            <form wire:submit.prevent="update" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-pencil"></i> Datos del Protocolo</h4>

                <div class="g_margin_bottom_10">
                    <label>Título del Protocolo / Discurso <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="titulo" placeholder="Ej: Discurso de Bienvenida">
                    @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10">
                    <label>Contenido / Texto Completo <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="contenido" rows="15"
                        placeholder="Escriba aquí el guión o pasos a seguir..."></textarea>
                    @error('contenido') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i> Guardar
                            Cambios</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.protocolo.todo', $evento->id) }}" class="g_boton cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información Adicional</h4>
                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Creado</p>
                <p class="g_negrita" style="margin:0 0 12px 0;">{{ $protocolo->created_at->format('d/m/Y H:i') }}</p>

                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Última Actualización</p>
                <p class="g_negrita" style="margin:0;">{{ $protocolo->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

</div>