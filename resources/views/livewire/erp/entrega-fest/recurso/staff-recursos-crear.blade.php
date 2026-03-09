<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Añadir Recurso
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
            <form wire:submit.prevent="store" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-file-arrow-up"></i> Datos del Recurso</h4>

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
                    <label>Archivo (PDF o Imagen) <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="file" wire:model="archivo">
                    <div wire:loading wire:target="archivo" class="g_inferior">Subiendo archivo...</div>
                    @error('archivo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store"><i class="fa-solid fa-save"></i> Guardar
                            Recurso</span>
                        <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i>
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
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Ayuda</h4>
                <p class="g_panel_parrafo">Los recursos son documentos de apoyo para el staff:</p>
                <ul class="g_lista_check">
                    <li><i class="fa-solid fa-check"></i> Mapas de ubicación.</li>
                    <li><i class="fa-solid fa-check"></i> Manuales operativos.</li>
                    <li><i class="fa-solid fa-check"></i> Fotos de referencia del montaje.</li>
                </ul>
            </div>
        </div>
    </div>

</div>