<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Editar Bloque de Itinerario
        </h2>
        <div class="cabecera_titulo_botones">
            <button type="button" class="g_boton danger"
                onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarBloqueOn', titulo: 'Eliminar Bloque', texto: 'Esta accion no se puede deshacer.' })">
                <i class="fa-solid fa-trash"></i> Eliminar
            </button>
            <a href="{{ route('erp.entrega-fest.vista.staff.itinerario', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="g_fila">

        {{-- COLUMNA PRINCIPAL: Formulario --}}
        <div class="g_columna_8">
            <form wire:submit.prevent="update" class="formulario g_panel g_gap_pagina">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-pencil"></i> Datos del Bloque</h4>

                <div class="g_margin_bottom_10">
                    <label>Titulo del Bloque <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <input type="text" wire:model="titulo" class="@error('titulo') input-error @enderror">
                    @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Hora de Inicio <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="time" wire:model="hora_inicio" class="@error('hora_inicio') input-error @enderror">
                        @error('hora_inicio') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Hora de Fin</label>
                        <input type="time" wire:model="hora_fin">
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Ubicacion / Zona</label>
                        <input type="text" wire:model="ubicacion" placeholder="Ej: Puerta principal">
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Responsable / Rol</label>
                        <input type="text" wire:model="responsable_rol" placeholder="Ej: Coordinador General">
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripcion del Bloque</label>
                    <textarea wire:model="descripcion" rows="3"></textarea>
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Orden de Aparicion</label>
                        <input type="number" wire:model="orden" min="0" style="width:120px;">
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Estado Actual</label>
                        <select wire:model="estado" class="@error('estado') select-error @enderror">
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="EN_CURSO">En Curso</option>
                            <option value="COMPLETADO">Completado</option>
                        </select>
                        @error('estado') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i>
                            Actualizar</span>
                        <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                            Guardando...</span>
                    </button>
                    <a href="{{ route('erp.entrega-fest.vista.staff.itinerario', $evento->id) }}"
                        class="g_boton cancelar">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>

        {{-- COLUMNA LATERAL --}}
        <div class="g_columna_4 g_gap_pagina">

            {{-- Resumen del Bloque --}}
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Resumen</h4>

                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Bloque ID</p>
                <p class="g_negrita" style="margin:0 0 12px 0;">#{{ $bloque->id }}</p>

                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Estado</p>
                <span
                    class="g_badge {{ $estado === 'COMPLETADO' ? 'success' : ($estado === 'EN_CURSO' ? 'warning' : 'light') }} g_mayuscula">
                    {{ $estado }}
                </span>

                <div style="margin-top:12px; border-top:1px solid var(--borde-card-color, #e5e7eb); padding-top:12px;">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Creado</p>
                    <p class="g_inferior" style="margin:0;">{{ $bloque->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- CHECKLIST --}}
            <div class="g_panel g_gap_pagina" style="gap:8px;">
                <h4 class="g_panel_titulo">
                    <i class="fa-solid fa-list-check"></i> Checklist
                    <span class="g_badge light" style="font-size:11px; margin-left:6px;">
                        {{ $bloque->checklists->where('esta_listo', true)->count() }}/{{ $bloque->checklists->count() }}
                    </span>
                </h4>

                {{-- Lista de tareas --}}
                @forelse($bloque->checklists as $item)
                    <div wire:key="task-{{ $item->id }}"
                        style="display:flex; align-items:center; gap:8px; border-bottom:1px solid var(--borde-card-color, #f0f0f0); padding-bottom:8px;">
                        <button wire:click="toggleTarea({{ $item->id }})"
                            class="g_boton {{ $item->esta_listo ? 'success' : 'light' }}"
                            style="width:28px; height:28px; padding:0; min-width:28px; border-radius:50%; flex-shrink:0;">
                            <i class="fa-solid {{ $item->esta_listo ? 'fa-check' : 'fa-circle' }}"
                                style="font-size:11px;"></i>
                        </button>
                        <span
                            style="flex:1; {{ $item->esta_listo ? 'text-decoration:line-through; opacity:0.5;' : '' }} font-size:13px;">
                            {{ $item->tarea }}
                        </span>
                        <button wire:click="eliminarTarea({{ $item->id }})" class="g_accion eliminar"
                            title="Eliminar tarea">
                            <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
                        </button>
                    </div>
                @empty
                    <div class="g_alerta info" style="padding:8px 12px; font-size:12px;">
                        <i class="fa-solid fa-circle-info"></i> Sin tareas. Agrega la primera.
                    </div>
                @endforelse

                {{-- Agregar nueva tarea --}}
                <div style="display:flex; gap:8px; margin-top:4px;">
                    <input type="text" wire:model="nueva_tarea" wire:keydown.enter.prevent="agregarTarea"
                        placeholder="Nueva tarea..." style="flex:1; font-size:13px;">
                    <button wire:click="agregarTarea" class="g_boton guardar" wire:loading.attr="disabled"
                        wire:target="agregarTarea">
                        <span wire:loading.remove wire:target="agregarTarea"><i class="fa-solid fa-plus"></i></span>
                        <span wire:loading wire:target="agregarTarea"><i class="fa-solid fa-spinner fa-spin"></i></span>
                    </button>
                </div>
                @error('nueva_tarea') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

</div>