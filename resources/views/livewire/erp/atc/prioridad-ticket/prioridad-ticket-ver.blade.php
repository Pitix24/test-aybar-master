<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Prioridad de Ticket</h2>

        <div class="cabecera_titulo_botones">
            @can('prioridad-ticket.vista-lista')
                <a href="{{ route('erp.prioridad-ticket.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('prioridad-ticket.vista-editar')
                <a href="{{ route('erp.prioridad-ticket.vista.editar', $prioridad->id) }}" class="g_boton primary">
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
                                <input type="checkbox" {{ $prioridad->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>
                            <span class="g_switch-label">
                                {{ $prioridad->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre</label>
                            <input type="text" value="{{ $prioridad->nombre }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Tiempo Permitido (Horas)</label>
                            <input type="text" value="{{ $prioridad->tiempo_permitido }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Color Informativo</label>
                            <input type="color" value="{{ $prioridad->color }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Icono (FontAwesome)</label>
                            <input type="text" value="{{ $prioridad->icono }}" readonly disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>