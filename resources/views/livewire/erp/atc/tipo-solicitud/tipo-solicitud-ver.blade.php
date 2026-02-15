<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Tipo de Solicitud</h2>

        <div class="cabecera_titulo_botones">
            @can('tipo-solicitud.lista')
                <a href="{{ route('erp.tipo-solicitud.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('tipo-solicitud.editar')
                <a href="{{ route('erp.tipo-solicitud.vista.editar', $tipo->id) }}" class="g_boton primary">
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
                                <input type="checkbox" {{ $tipo->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre</label>
                            <input type="text" value="{{ $tipo->nombre }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Tiempo Solución (Horas)</label>
                            <input type="text" value="{{ $tipo->tiempo_solucion }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Áreas Relacionadas</label>
                        <div class="g_grid_permisos">
                            @foreach ($tipo->areas as $area)
                                <div class="permiso_item">
                                    <label class="cursor_pointer">
                                        <input type="checkbox" checked disabled>
                                        <span class="fw-bold">{{ $area->nombre }}</span>
                                    </label>
                                </div>
                            @endforeach
                            @if($tipo->areas->isEmpty())
                                <p>No hay áreas vinculadas.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>