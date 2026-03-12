<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update" message="Actualizando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Editar Bloque de Itinerario
        </h2>

        <div class="cabecera_titulo_botones">
            @can('itinerario.lista')
                <a href="{{ route('erp.entrega-fest.itinerario.todo', $evento->id) }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('entrega-fest.ver-staff')
                <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                    <i class="fa-solid fa-grip"></i> Panel de Staff
                </a>
            @endcan

            @can('itinerario.eliminar')
                <button type="button" class="g_boton danger"
                    onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarBloqueOn', titulo: 'Eliminar Bloque', texto: 'Esta accion no se puede deshacer.' })">
                    Eliminar <i class="fa-solid fa-trash"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
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

                <div class="g_margin_bottom_10">
                    <label>Ubicación / Zona</label>
                    <input type="text" wire:model="ubicacion" placeholder="Ej: Puerta principal">
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
                            <option value="CURSO">En Curso</option>
                            <option value="COMPLETADO">Completado</option>
                        </select>
                        @error('estado') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="formulario_botones">
                    @can('itinerario.editar')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="update"><i class="fa-solid fa-save"></i>
                                Actualizar</span>
                            <span wire:loading wire:target="update"><i class="fa-solid fa-spinner fa-spin"></i>
                                Actualizando...</span>
                        </button>
                    @endcan

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div class="g_columna_4 g_gap_pagina">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Resumen</h4>

                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Bloque ID</p>
                <p class="g_negrita" style="margin:0 0 12px 0;">#{{ $bloque->id }}</p>

                <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Estado</p>
                <span
                    class="g_badge {{ $estado === \App\Models\EntregaFestItinerarioBloque::ESTADO_COMPLETADO ? 'success' : ($estado === \App\Models\EntregaFestItinerarioBloque::ESTADO_CURSO ? 'warning' : 'light') }} g_mayuscula">
                    {{ $estado }}
                </span>

                <div style="margin-top:12px; border-top:1px solid var(--borde-card-color, #e5e7eb); padding-top:12px;">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">Creado</p>
                    <p class="g_inferior" style="margin:0;">{{ $bloque->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="g_panel g_gap_pagina" style="gap:8px;">
                <h4 class="g_panel_titulo">
                    <i class="fa-solid fa-list-check"></i> Checklist
                    <span class="g_badge light" style="font-size:11px; margin-left:6px;">
                        {{ $bloque->checklists->where('esta_listo', true)->count() }}/{{ $bloque->checklists->count() }}
                    </span>
                </h4>

                @forelse($bloque->checklists as $item)
                    <div wire:key="task-{{ $item->id }}"
                        style="display:flex; align-items:center; gap:8px; border-bottom:1px solid var(--borde-card-color, #f0f0f0); padding-bottom:8px;">
                        <div style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fa-solid {{ $item->esta_listo ? 'fa-check-circle text-success' : 'fa-circle text-muted' }}"
                                style="font-size:14px; color: {{ $item->esta_listo ? 'var(--color-success)' : '#ccc' }};"></i>
                        </div>
                        <span
                            style="flex:1; {{ $item->esta_listo ? 'text-decoration:line-through; opacity:0.5;' : '' }} font-size:13px;">
                            {{ $item->tarea }}
                        </span>
                        @can('itinerario.eliminar-tarea')
                            <button wire:click="eliminarTarea({{ $item->id }})" class="g_accion eliminar"
                                title="Eliminar tarea">
                                <i class="fa-solid fa-xmark" style="font-size:12px;"></i>
                            </button>
                        @endcan
                    </div>
                @empty
                    <div class="g_alerta info" style="padding:8px 12px; font-size:12px;">
                        <i class="fa-solid fa-circle-info"></i> Sin tareas. Agrega la primera.
                    </div>
                @endforelse

                <div style="display:flex; gap:8px; margin-top:4px;">
                    <input type="text" wire:model="nueva_tarea" wire:keydown.enter.prevent="agregarTarea"
                        placeholder="Nueva tarea..." style="flex:1; font-size:13px;">

                    @can('itinerario.crear-tarea')
                        <button wire:click="agregarTarea" class="g_boton guardar" wire:loading.attr="disabled"
                            wire:target="agregarTarea">
                            <span wire:loading.remove wire:target="agregarTarea"><i class="fa-solid fa-plus"></i></span>
                            <span wire:loading wire:target="agregarTarea"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>
                    @endcan
                </div>
                @error('nueva_tarea') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

        </div>
    </div>

</div>