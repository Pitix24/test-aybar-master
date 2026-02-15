<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Permiso</h2>

        <div class="cabecera_titulo_botones">
            @can('permiso.ver')
                <a href="{{ route('erp.permiso.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('permiso.editar')
                <a href="{{ route('erp.permiso.vista.editar', $permission->id) }}" class="g_boton primary">
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

                    <div class="g_margin_bottom_10">
                        <label for="name">Nombre del Permiso</label>
                        <input type="text" id="name" value="{{ $permission->name }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="module">Módulo</label>
                        <input type="text" id="module" value="{{ $permission->module }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Guard</label>
                        <input type="text" value="{{ $permission->guard_name }}" readonly disabled>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Fecha de Creación</label>
                        <p>{{ $permission->created_at->format('d/m/Y H:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>