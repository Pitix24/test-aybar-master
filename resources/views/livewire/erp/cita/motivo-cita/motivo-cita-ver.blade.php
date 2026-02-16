<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Motivo de Cita</h2>

        <div class="cabecera_titulo_botones">
            @can('motivo-cita.lista')
                <a href="{{ route('erp.motivo-cita.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('motivo-cita.editar')
                <a href="{{ route('erp.motivo-cita.vista.editar', $motivo->id) }}" class="g_boton primary">
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
                                <input type="checkbox" {{ $motivo->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $motivo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Nombre del Motivo</label>
                        <input type="text" value="{{ $motivo->nombre }}" readonly disabled>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Color Informativo</label>
                            <input type="color" value="{{ $motivo->color }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Icono (FontAwesome)</label>
                            <input type="text" value="{{ $motivo->icono }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>