<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Ítem de Menú</h2>

        <div class="cabecera_titulo_botones">
            @can('menu.lista')
                <a href="{{ route('erp.menu.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('menu.editar')
                <a href="{{ route('erp.menu.vista.editar', $menu->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel" x-data="{ activeTab: 'general' }">

                    <div class="g_tab_navegacion">
                        <div class="g_tab_botones">
                            <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-info-circle"></i> Información General
                            </button>

                            <button type="button" @click="activeTab = 'rutas'"
                                :class="activeTab === 'rutas' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-route"></i> Rutas y Enlaces
                            </button>

                            <button type="button" @click="activeTab = 'seguridad'"
                                :class="activeTab === 'seguridad' ? 'g_tab_active' : 'g_tab_inactive'"
                                class="g_tab_boton">
                                <i class="fa-solid fa-shield-halved"></i> Seguridad
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">

                        <div class="g_margin_bottom_10">
                            <label for="estado_activo">Estado</label>

                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo" type="checkbox" @checked($menu->activo) readonly disabled>
                                    <span class="g_switch-slider"></span>
                                </label>

                                <span class="g_switch-label">
                                    {{ $menu->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="nombre">Nombre del Ítem</label>
                                <input type="text" id="nombre" value="{{ $menu->nombre }}" readonly disabled>
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="parent_id">Ítem Padre</label>
                                <input type="text" id="parent_id"
                                    value="{{ $menu->parent->nombre ?? '-- Sin Padre (Nivel 1) --' }}" readonly
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_4 g_margin_bottom_10">
                                <label for="icono">Icono (FontAwesome)</label>
                                <input type="text" id="icono" value="{{ $menu->icono }}" readonly disabled>
                            </div>

                            <div class="g_columna_4 g_margin_bottom_10">
                                <label for="orden">Orden</label>
                                <input type="number" id="orden" value="{{ $menu->orden }}" readonly disabled>
                            </div>

                            <div class="g_columna_4 g_margin_bottom_10">
                                <label for="nivel">Nivel (Auto)</label>
                                <input type="text" id="nivel" value="{{ $menu->nivel }}" readonly disabled>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'rutas'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="ruta">Ruta de Laravel (Route Name)</label>
                                <input type="text" id="ruta" value="{{ $menu->ruta ?? '-' }}" readonly disabled>
                                <p class="leyenda">Deja vacío si es un ítem agrupador (sin acción).</p>
                            </div>

                            <div class="g_columna_6 g_margin_bottom_10">
                                <label for="url">URL Manual (Externa)</label>
                                <input type="text" id="url" value="{{ $menu->url ?? '-' }}" readonly disabled>
                                <p class="leyenda">Solo para enlaces externos. No usar con Ruta.</p>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'seguridad'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_columna_12 g_margin_bottom_10">
                                <label for="permiso">Permiso Requerido (Spatie)</label>
                                <input type="text" id="permiso"
                                    value="{{ $menu->permiso ?? '-- Sin Permiso (Público) --' }}" readonly disabled>
                                <p class="leyenda">Si se deja vacío, el ítem será visible para cualquier usuario
                                    autenticado.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>