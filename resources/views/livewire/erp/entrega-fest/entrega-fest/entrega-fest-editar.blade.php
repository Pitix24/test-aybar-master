<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, agregarProyecto, quitarProyecto" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Entrega Fest</h2>

        <div class="cabecera_titulo_botones">
            @can('entrega-fest.lista')
                <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('entrega-fest.ver-panel')
                <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                    <i class="fa-solid fa-grip"></i> Panel de Gestión
                </a>
            @endcan

            @can('entrega-fest.eliminar')
                <button type="button" class="g_boton danger" onclick="confirmarEliminarCanal()">
                    Eliminar <i class="fa-solid fa-trash-can"></i>
                </button>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila" x-data="{ activeTab: 'general' }">
        <div class="g_columna_8">
            <div class="g_panel" style="padding: 0;">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-circle-info"></i> Gral.
                        </button>
                        <button type="button" @click="activeTab = 'mensajeria'"
                            :class="activeTab === 'mensajeria' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-envelope-open-text"></i> Mensajería
                        </button>
                        <button type="button" @click="activeTab = 'prospectos'"
                            :class="activeTab === 'prospectos' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-users"></i> Prospectos
                        </button>
                        <button type="button" @click="activeTab = 'itinerario'"
                            :class="activeTab === 'itinerario' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock"></i> Cronograma
                        </button>
                        <button type="button" @click="activeTab = 'mop'"
                            :class="activeTab === 'mop' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-tasks"></i> Tareas MOP
                        </button>
                    </div>
                </div>

                <!-- TAB: GENERAL -->
                <div x-show="activeTab === 'general'" x-transition class="g_tab_content" style="padding: 20px;">
                    <form wire:submit.prevent="update" class="formulario">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Información del Evento</h4>

                        <div class="g_margin_bottom_10">
                            <label for="estado_activo">Estado</label>
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">{{ $activo ? 'Activo' : 'Inactivo' }}</span>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombre del Evento</label>
                                <input type="text" wire:model="nombre">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Código único</label>
                                <input type="text" wire:model="codigo">
                            </div>
                        </div>

                        <div class="g_margin_bottom_10">
                            <label>Descripción General</label>
                            <textarea wire:model="descripcion" rows="3"></textarea>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Fecha de Entrega</label>
                                <input type="date" wire:model="fecha_entrega">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Responsable</label>
                                <select wire:model="gestor_id">
                                    <option value="">Seleccione...</option>
                                    @foreach ($gestores as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="formulario_botones g_margin_top_10">
                            <button type="submit" class="g_boton guardar">
                                <i class="fa-solid fa-save"></i> Actualizar Información
                            </button>
                        </div>
                    </form>

                    @if (!empty($proyectos_agregados))
                        <div class="g_margin_top_20">
                            <h4 class="g_panel_titulo"><i class="fa-solid fa-layer-group"></i> Proyectos vinculados</h4>
                            <div class="g_contenedor_tabla">
                                <table class="g_tabla">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>Proyecto</th>
                                            <th class="g_celda_centro">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proyectos_agregados as $p)
                                            <tr wire:key="p-agregado-{{ $p['id'] }}">
                                                <td class="g_negrita">{{ $p['unidad_negocio_nombre'] }}</td>
                                                <td>ID: {{ $p['id'] }} - {{ $p['nombre'] }} </td>
                                                <td class="g_celda_centro">
                                                    <button type="button" wire:click="quitarProyecto({{ $p['id'] }})"
                                                        class="g_boton danger small">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- TAB: MENSAJERÍA -->
                <div x-show="activeTab === 'mensajeria'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-mensaje', ['evento' => $evento])
                </div>

                <!-- TAB: PROSPECTOS -->
                <div x-show="activeTab === 'prospectos'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-importar-prospecto', ['evento' => $evento])
                </div>

                <!-- TAB: ITINERARIO -->
                <div x-show="activeTab === 'itinerario'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-importar-itinerario', ['evento' => $evento])
                </div>

                <!-- TAB: TAREAS MOP -->
                <div x-show="activeTab === 'mop'">
                    @livewire('erp.entrega-fest.entrega-fest.entrega-fest-importar-mop', ['evento' => $evento])
                </div>

            </div>
        </div>

        <!-- BARRA LATERAL -->
        <div class="g_columna_4">
            <div class="g_panel formulario" x-show="activeTab === 'general'">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-diagram-project"></i> Gestión de Proyectos</h4>
                <div class="g_margin_bottom_10">
                    <label>Unidad de Negocio</label>
                    <select wire:model.live="unidad_negocio_id">
                        <option value="">Seleccione...</option>
                        @foreach ($unidades_negocios as $u)
                            <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="g_margin_bottom_10">
                    <label>Proyecto</label>
                    <select wire:model="proyecto_id" {{ !$unidad_negocio_id ? 'disabled' : '' }}>
                        <option value="">Seleccione proyecto...</option>
                        @foreach ($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="agregarProyecto" class="g_boton guardar g_ancho_completo">
                    <i class="fa-solid fa-plus"></i> Vincular Proyecto
                </button>
            </div>

            <div class="g_panel" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;"
                x-show="activeTab !== 'general'">
                <h4 class="g_panel_titulo" style="color: #166534;"><i class="fa-solid fa-circle-info"></i> Resumen</h4>
                <p class="leyenda">Editando evento: <b>{{ $nombre }}</b>.</p>
                <p class="leyenda">Toda la configuración de esta pestaña se guarda independientemente.</p>
            </div>
        </div>
    </div>
</div>