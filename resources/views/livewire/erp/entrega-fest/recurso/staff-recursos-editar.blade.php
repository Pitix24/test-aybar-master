<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Editar Recurso
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.recurso.todo', $evento->id) }}" class="g_boton light">
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
                <h4 class="g_panel_titulo"><i class="fa-solid fa-pencil"></i> Datos del Recurso</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Nombre del Documento / Mapa <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" wire:model="nombre_publico" placeholder="Ej: Plano de Aforos">
                        @error('nombre_publico') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Tipo de Recurso <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="tipo_recurso">
                            <option value="MAPA">Mapa / Plano</option>
                            <option value="MANUAL">Manual / Guía</option>
                            <option value="FOTO">Fotografía / Referencia</option>
                        </select>
                        @error('tipo_recurso') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Actualizar Archivo (Opcional)</label>
                    <input type="file" wire:model="archivo">
                    <div wire:loading wire:target="archivo" class="g_inferior">Subiendo archivo...</div>
                    @error('archivo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i> Guardar
                            Cambios</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.recurso.todo', $evento->id) }}" class="g_boton cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-file-pdf"></i> Archivo Actual</h4>
                @if($recurso->media->count() > 0)
                    <div style="margin-top:15px; text-align:center;">
                        <a href="{{ $recurso->getFirstMediaUrl('recursos') ?: $recurso->getFirstMediaUrl() }}"
                            target="_blank">
                            <i class="fa-solid fa-file-pdf" style="font-size:4rem; color:var(--color-danger);"></i>
                            <p class="g_negrita" style="margin-top:10px;">Ver Archivo Actual</p>
                        </a>
                    </div>
                @else
                    <p class="g_inferior">No hay archivo cargado.</p>
                @endif
            </div>
        </div>
    </div>

</div>