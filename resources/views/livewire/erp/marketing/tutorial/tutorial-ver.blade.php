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
                        <div class="g_valor_ver"
                            style="min-height: 120px; align-items: flex-start; padding-top: 10px; white-space: pre-line;">
                            {{ $tutorial->descripcion ?: 'Sin descripción registrada.' }}
                        </div>
                    </div>

                    <h4 class="g_panel_titulo g_margin_top_20">Miniatura</h4>
                    @if ($tutorial->miniatura)
                        <div
                            style="border: 1px solid #eee; padding: 5px; border-radius: 8px; background: #fff; max-width: 300px;">
                            <img src="{{ $tutorial->miniatura->url ?? asset($tutorial->miniatura->path) }}"
                                style="width: 100%; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        </div>
                    @else
                        <div class="g_vacio_pequeno" style="padding: 30px; border: 1px dashed #eee; border-radius: 8px;">
                            <i class="fa-solid fa-image-slash"
                                style="font-size: 2rem; color: #eee; margin-bottom: 10px;"></i>
                            <p style="color: #bbb; font-size: 0.9rem;">Sin miniatura</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .g_valor_ver {
            padding: 10px 15px;
            background: #fdfdfd;
            border-radius: 8px;
            border: 1px solid #f0f0f0;
            color: #333;
            min-height: 42px;
            display: flex;
            align-items: center;
        }
    </style>
</div>