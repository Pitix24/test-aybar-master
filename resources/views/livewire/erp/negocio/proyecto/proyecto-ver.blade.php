<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Proyecto</h2>

        <div class="cabecera_titulo_botones">
            @can('proyecto.lista')
                <a href="{{ route('erp.proyecto.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('proyecto.editar')
                <a href="{{ route('erp.proyecto.vista.editar', $proyecto->id) }}" class="g_boton primary">
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
                                <i class="fa-solid fa-building"></i> Información General
                            </button>

                            <button type="button" @click="activeTab = 'slin'"
                                :class="activeTab === 'slin' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-user-tie"></i> SLIN
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" class="g_tab_content">
                        <div class="g_margin_bottom_20">
                            <label>Estado</label>
                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input type="checkbox" {{ $proyecto->activo ? 'checked' : '' }} disabled>
                                    <span class="g_switch-slider"></span>
                                </label>
                                <span class="g_switch-label">
                                    {{ $proyecto->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Unidad de Negocio</label>
                                <input type="text" value="{{ $proyecto->unidadNegocio->nombre ?? '-' }}" readonly
                                    disabled>
                            </div>

                            <div class="g_columna_6 g_margin_bottom_20">
                                <label>Grupo de Proyecto</label>
                                <input type="text" value="{{ $proyecto->grupoProyecto->nombre ?? '-' }}" readonly
                                    disabled>
                            </div>
                        </div>

                        <div class="g_margin_bottom_20">
                            <label>Nombre del Proyecto</label>
                            <input type="text" value="{{ $proyecto->nombre }}" readonly disabled>
                        </div>
                    </div>

                    <div x-show="activeTab === 'slin'" class="g_tab_content">
                        <div class="g_margin_bottom_20">
                            <label>SLIN ID</label>
                            <input type="text" value="{{ $proyecto->slin_id ?? '-' }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>