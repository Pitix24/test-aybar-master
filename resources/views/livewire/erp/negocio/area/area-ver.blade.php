<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Área</h2>

        <div class="cabecera_titulo_botones">
            @can('area.lista')
                <a href="{{ route('erp.area.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('area.editar')
                <a href="{{ route('erp.area.vista.editar', $area_model->id) }}" class="g_boton primary">
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
                        <label for="estado_activo">
                            Estado
                        </label>

                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" @checked($area_model->activo) disabled>
                                <span class="g_switch-slider"></span>
                            </label>

                            <span class="g_switch-label">
                                {{ $area_model->activo ? 'Activo' : 'Desactivado' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre del Área</label>
                            <input type="text" value="{{ $area_model->nombre }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Email Buzón (ATC)</label>
                            <input type="email" value="{{ $area_model->email_buzon ?? '-' }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Color Representativo</label>
                            <input type="color" value="{{ $area_model->color }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Icono</label>
                            <input type="text" value="{{ $area_model->icono }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Sedes vinculadas</label>
                        <div class="g_fila">
                            @foreach ($area_model->sedes as $sede)
                                <div class="g_columna_4">
                                    <label class="g_checkbox">
                                        <input type="checkbox" checked disabled>
                                        <span>{{ $sede->nombre }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>