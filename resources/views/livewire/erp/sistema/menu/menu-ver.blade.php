<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Ítem de Menú</h2>

        <div class="cabecera_titulo_botones">
            @can('menu.ver')
                <a href="{{ route('erp.menu.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('menu.editar')
                <a href="{{ route('erp.menu.vista.editar', $menu->id) }}" class="g_boton primary">
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
                    <h4 class="g_panel_titulo">General</h4>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre del Ítem</label>
                            <input type="text" value="{{ $menu->nombre }}" readonly disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Ítem Padre</label>
                            <input type="text" value="{{ $menu->parent->nombre ?? 'Nivel Raíz' }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Icono</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="text" value="{{ $menu->icono }}" readonly disabled>
                                <div class="g_panel" style="padding: 10px; margin:0;">
                                    <i class="{{ $menu->icono ?: 'fa-solid fa-question' }} fa-lg"></i>
                                </div>
                            </div>
                        </div>
                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Orden</label>
                            <input type="text" value="{{ $menu->orden }}" readonly disabled>
                        </div>
                        <div class="g_columna_4 g_margin_bottom_10">
                            <label>Nivel</label>
                            <input type="text" value="{{ $menu->nivel }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Ruta (Route Name)</label>
                            <input type="text" value="{{ $menu->ruta ?? '-' }}" readonly disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>URL Externa</label>
                            <input type="text" value="{{ $menu->url ?? '-' }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Permiso Requerido</label>
                            <input type="text" value="{{ $menu->permiso ?? 'Público' }}" readonly disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Estado</label>
                            <br>
                            @if($menu->activo)
                                <span class="g_badge success">Activo</span>
                            @else
                                <span class="g_badge danger">Inactivo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>