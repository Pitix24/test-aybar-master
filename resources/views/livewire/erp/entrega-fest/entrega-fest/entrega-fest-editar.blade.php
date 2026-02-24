<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, agregarProyecto, quitarProyecto" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Entrega Fest: <span style="color: var(--color-primary);">{{ $nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>


    <div class="g_fila" x-data="{ activeTab: 'general' }">
        <div class="g_columna_8">
            <form wire:submit.prevent="update" class="formulario g_panel">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-file-invoice"></i> Datos del Evento
                        </button>

                        <button type="button" @click="activeTab = 'proyectos'"
                            :class="activeTab === 'proyectos' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-layer-group"></i> Proyectos Vinculados
                            ({{ count($proyectos_agregados) }})
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_margin_bottom_15">
                        <label for="estado_activo">
                            Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                        @error('activo') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_15 g_columna_6">
                            <label>Nombre del Evento <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="nombre" class="@error('nombre') input-error @enderror">
                            @error('nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_15 g_columna_6">
                            <label>Código Único <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" wire:model="codigo" class="@error('codigo') input-error @enderror">
                            @error('codigo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_15">
                        <label>Descripción</label>
                        <textarea wire:model="descripcion" rows="4"></textarea>
                        @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_15 g_columna_6">
                            <label>Fecha de Entrega <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="date" wire:model="fecha_entrega"
                                class="@error('fecha_entrega') input-error @enderror">
                            @error('fecha_entrega') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>

                        <div class="g_margin_bottom_15 g_columna_6">
                            <label>Responsable <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <select wire:model="gestor_id" class="@error('gestor_id') select-error @enderror">
                                <option value="">Seleccione...</option>
                                @foreach ($gestores as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'proyectos'" x-transition class="g_tab_content">
                    @if (empty($proyectos_agregados))
                        <div class="g_vacio">
                            <i class="fa-solid fa-folder-open"></i>
                            <p>No hay proyectos vinculados a este evento.</p>
                        </div>
                    @else
                        <div class="g_contenedor_tabla">
                            <table class="g_tabla">
                                <thead>
                                    <tr>
                                        <th>Unidad de Negocio</th>
                                        <th>Proyecto</th>
                                        <th class="g_celda_centro">Remover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proyectos_agregados as $p)
                                        <tr wire:key="p-agregado-{{ $p['id'] }}">
                                            <td class="g_negrita">{{ $p['unidad_negocio_nombre'] }}</td>
                                            <td>{{ $p['nombre'] }}</td>
                                            <td class="g_celda_centro">
                                                <button type="button" wire:click="quitarProyecto({{ $p['id'] }})"
                                                    class="g_accion eliminar" title="Quitar Proyecto">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @error('proyectos_agregados')
                        <p class="mensaje_error" style="margin-top: 15px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="fa-solid fa-save"></i> Guardar Cambios
                        </span>
                        <span wire:loading wire:target="update">
                            <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                        </span>
                    </button>

                    <button type="button" class="g_boton cancelar" onclick="history.back()">
                        <i class="fa-solid fa-times"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div class="g_columna_4">
            <div class="g_panel formulario">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-sliders"></i> Vincular Proyecto</h4>

                <div class="g_margin_bottom_15">
                    <label>Unidad de Negocio <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model.live="unidad_negocio_id"
                        class="@error('unidad_negocio_id') select-error @enderror">
                        <option value="">Seleccione...</option>
                        @foreach ($unidades_negocios as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                    @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_margin_bottom_20">
                    <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                    <select wire:model="proyecto_id" {{ !$unidad_negocio_id ? 'disabled' : '' }}
                        class="@error('proyecto_id') select-error @enderror">
                        <option value="">Seleccione un proyecto...</option>
                        @foreach ($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                    @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="formulario_botones">
                    <button type="button" wire:click="agregarProyecto" class="g_boton primary"
                        wire:loading.attr="disabled" wire:target="agregarProyecto" style="width: 100%;">
                        <span wire:loading.remove wire:target="agregarProyecto">
                            <i class="fa-solid fa-plus"></i> Vincular
                        </span>
                        <span wire:loading wire:target="agregarProyecto">
                            Vinculando...
                        </span>
                    </button>
                </div>
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-chart-line"></i> Resumen</h4>
                <div class="g_fila">
                    <div class="g_columna_6">
                        <p style="font-size: 0.8rem; color: #666;">Proyectos</p>
                        <p class="g_negrita" style="font-size: 1.2rem;">{{ count($proyectos_agregados) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>