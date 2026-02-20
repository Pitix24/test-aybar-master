<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Tutorial</h2>

        <div class="cabecera_titulo_botones">
            @can('tutorial.lista')
                <a href="{{ route('erp.tutorial.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('tutorial.editar')
                <a href="{{ route('erp.tutorial.vista.editar', $tutorial->id) }}" class="g_boton primary">
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
                                <input type="checkbox" {{ $tutorial->activo ? 'checked' : '' }} disabled>
                                <span class="g_switch-slider"></span>
                            </label>

                            <span class="g_switch-label">
                                {{ $tutorial->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Título del Tutorial</label>
                        <input type="text" value="{{ $tutorial->titulo }}" readonly disabled>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>ID de Video (YouTube)</label>
                            <input type="text" value="{{ $tutorial->video_id }}" readonly disabled>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>Orden de visualización</label>
                            <input type="text" value="{{ $tutorial->orden }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>

                        <textarea name="descripcion" id="descripcion" readonly rows="5"
                            disabled>{{ $tutorial->descripcion ?: 'Sin descripción registrada.' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Miniatura del Tutorial</h4>

                    <div class="contenedor_dropzone mediano">
                        @if ($tutorial->miniatura)
                            <img src="{{ $tutorial->miniatura->url ?? asset($tutorial->miniatura->path) }}"
                                class="g_imagen_actual">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>