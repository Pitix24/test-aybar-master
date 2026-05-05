<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Unidad de Negocio</h2>

        <div class="cabecera_titulo_botones">
            @can('unidad-negocio.lista')
            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            @can('unidad-negocio.editar')
            <a href="{{ route('erp.unidad-negocio.vista.editar', $unidad_model->id) }}" class="g_boton primary">
                Editar <i class="fa-solid fa-pencil"></i>
            </a>
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

                            <button type="button" @click="activeTab = 'cavali'"
                                :class="activeTab === 'cavali' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-user-tie"></i> Representante Legal CAVALI
                            </button>

                            <button type="button" @click="activeTab = 'slin'"
                                :class="activeTab === 'slin' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                                <i class="fa-solid fa-server"></i> SLIN
                            </button>
                        </div>
                    </div>

                    <div x-show="activeTab === 'general'" x-transition class="g_tab_content">

                        <div class="g_margin_bottom_10">
                            <label for="estado_activo">Estado</label>

                            <div class="g_switch-wrapper">
                                <label class="g_switch">
                                    <input id="estado_activo" type="checkbox" @checked($unidad_model->activo) readonly
                                    disabled>
                                    <span class="g_switch-slider"></span>
                                </label>

                                <span class="g_switch-label">
                                    {{ $unidad_model->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombre</label>
                                <input type="text" value="{{ $unidad_model->nombre }}" readonly disabled>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Razón social</label>
                                <input type="text" value="{{ $unidad_model->razon_social }}" readonly disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>RUC</label>
                                <input type="text" value="{{ $unidad_model->ruc ?? '-' }}" readonly disabled>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Código</label>
                                <input type="text" value="{{ $unidad_model->codigo ?? '-' }}" readonly disabled>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_12">
                                <label>Dirección</label>
                                <input type="text" value="{{ $unidad_model->direccion ?? '-' }}" readonly disabled>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'cavali'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Tipo de Documento</label>
                                <input type="text" value="{{ $unidad_model->cavali_girador_tipo_documento ?? '-' }}"
                                    readonly disabled>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Número de Documento</label>
                                <input type="text" value="{{ $unidad_model->cavali_girador_documento ?? '-' }}" readonly
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombres</label>
                                <input type="text" value="{{ $unidad_model->cavali_girador_nombre ?? '-' }}" readonly
                                    disabled>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Apellidos</label>
                                <input type="text" value="{{ $unidad_model->cavali_girador_apellido ?? '-' }}" readonly
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Email</label>
                                <input type="text" value="{{ $unidad_model->cavali_girador_email ?? '-' }}" readonly
                                    disabled>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Teléfono</label>
                                <input type="text" value="{{ $unidad_model->cavali_girador_telefono ?? '-' }}" readonly
                                    disabled>
                            </div>
                        </div>
                    </div>

                    <div x-show="activeTab === 'slin'" x-transition class="g_tab_content">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>SLIN ID</label>
                                <input type="text" value="{{ $unidad_model->slin_id ?? '-' }}" readonly disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>