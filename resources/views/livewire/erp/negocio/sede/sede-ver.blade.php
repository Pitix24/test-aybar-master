<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Sede</h2>

        <div class="cabecera_titulo_botones">
            @can('sede.lista')
                <a href="{{ route('erp.sede.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('sede.editar')
                <a href="{{ route('erp.sede.vista.editar', $sede_model->id) }}" class="g_boton primary">
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
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

                    <div class="g_margin_bottom_10">
                        <label for="estado_activo">Estado</label>

                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" @checked($sede_model->activo) readonly
                                    disabled>
                                <span class="g_switch-slider"></span>
                            </label>

                            <span class="g_switch-label">
                                {{ $sede_model->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Nombre</label>
                        <input type="text" value="{{ $sede_model->nombre }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Dirección</label>
                        <textarea readonly disabled rows="3">{{ $sede_model->direccion ?? '-' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>