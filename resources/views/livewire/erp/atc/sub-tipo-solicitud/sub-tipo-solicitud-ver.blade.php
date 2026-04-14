<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Sub Tipo de Solicitud</h2>

        <div class="cabecera_titulo_botones">
            @can('sub-tipo-solicitud.vista-lista')
                <a href="{{ route('erp.sub-tipo-solicitud.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('sub-tipo-solicitud.vista-editar')
                <a href="{{ route('erp.sub-tipo-solicitud.vista.editar', $sub_tipo->id) }}" class="g_boton primary">
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
                                <input type="checkbox" {{ $sub_tipo->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $sub_tipo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Tipo de Solicitud</label>
                        <input type="text" value="{{ $sub_tipo->tipoSolicitud->nombre ?? '-' }}" readonly disabled>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre Sub Tipo</label>
                            <input type="text" value="{{ $sub_tipo->nombre }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Tiempo Solución (Horas)</label>
                            <input type="text" value="{{ $sub_tipo->tiempo_solucion ?? 'Heredado' }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>