<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Grupo</h2>

        <div class="cabecera_titulo_botones">
            @can('grupo-proyecto.lista')
                <a href="{{ route('erp.grupo-proyecto.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('grupo-proyecto.editar')
                <a href="{{ route('erp.grupo-proyecto.vista.editar', $grupo->id) }}" class="g_boton primary">
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

                    <div class="g_margin_bottom_20">
                        <label>Estado</label>
                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input type="checkbox" {{ $grupo->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $grupo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_20">
                        <label for="nombre">Nombre del Grupo</label>
                        <input type="text" id="nombre" value="{{ $grupo->nombre }}" readonly disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>