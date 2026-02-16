<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Estado de Cita</h2>

        <div class="cabecera_titulo_botones">
            @can('estado-cita.lista')
                <a href="{{ route('erp.estado-cita.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('estado-cita.editar')
                <a href="{{ route('erp.estado-cita.vista.editar', $estado->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i></a>
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
                        <label>Estado</label>
                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input type="checkbox" {{ $estado->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $estado->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Nombre del Estado</label>
                        <input type="text" value="{{ $estado->nombre }}" readonly disabled>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Color Informativo</label>
                            <input type="color" value="{{ $estado->color }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Icono (FontAwesome)</label>
                            <input type="text" value="{{ $estado->icono }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>