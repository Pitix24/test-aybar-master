<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Asignar Tarea MOP
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff.mop.tareas', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Volver a Lista
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <form wire:submit.prevent="store" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-list-check"></i> Nueva Tarea</h4>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Responsable <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="user_id" class="@error('user_id') select-error @enderror">
                            <option value="">Seleccione un usuario...</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_3 g_margin_bottom_10">
                        <label>Fase <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                        <select wire:model="fase">
                            <option value="ANTES">Antes del Evento</option>
                            <option value="DURANTE">Durante el Evento</option>
                            <option value="CIERRE">Cierre</option>
                        </select>
                    </div>
                </div>

                {{-- Importar desde plantilla --}}
                @if($plantillas->count())
                    <div class="g_resaltado_caja info" style="margin-bottom:4px;">
                        <span class="g_resaltado_caja_titulo"><i class="fa-solid fa-wand-magic-sparkles"></i> Importar desde
                            plantilla (opcional)</span>
                        <div style="display:flex; gap:8px; margin-top:8px;">
                            <select wire:model="plantilla_id" style="flex:1;">
                                <option value="">Seleccione una plantilla...</option>
                                @foreach($plantillas as $pl)
                                    <option value="{{ $pl->id }}">[{{ $pl->fase }}] {{ $pl->rol_nombre }} —
                                        {{ Str::limit($pl->instruccion, 50) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="importarPlantilla" class="g_boton info">
                                <i class="fa-solid fa-file-import"></i> Importar
                            </button>
                        </div>
                    </div>
                @endif

                <div class="g_margin_bottom_10">
                    <label>Titulo de la Tarea <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror"
                        placeholder="Ej: Verificar sonido del escenario principal">
                    @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_10">
                    <label>Instruccion detallada <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="instruccion" rows="4" class="@error('instruccion') input-error @enderror"
                        placeholder="Que debe hacer exactamente el responsable..."></textarea>
                    @error('instruccion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="store"><i class="fa-solid fa-save"></i> Guardar</span>
                        <span wire:loading wire:target="store"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.vista.staff.mop.tareas', $evento->id) }}"
                        class="g_boton cancelar"><i class="fa-solid fa-times"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>