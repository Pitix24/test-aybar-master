<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Tutorial</h2>

        <div class="cabecera_titulo_botones">
            @can('tutorial.lista')
                <a href="{{ route('erp.tutorial.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('tutorial.editar')
                <a href="{{ route('erp.tutorial.vista.editar', $tutorial->id) }}" class="g_boton guardar">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <!-- Columna Principal: Datos -->
        <div class="g_columna_8 g_gap_pagina">
            <div class="formulario g_panel" x-data="{ activeTab: 'general' }">
                <!-- Navegación por Tabs -->
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-file-lines"></i> Información General
                        </button>
                    </div>
                </div>

                <!-- Contenido del Tab -->
                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_12">
                            <label>Título del Tutorial</label>
                            <div class="g_valor_ver g_negrita">{{ $tutorial->titulo }}</div>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label>ID de Video (YouTube)</label>
                            <div class="g_valor_ver"><code>{{ $tutorial->video_id }}</code></div>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Orden</label>
                            <div class="g_valor_ver">{{ $tutorial->orden }}</div>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_3">
                            <label>Estado</label>
                            <div class="g_valor_ver">
                                <span class="g_badge {{ $tutorial->activo ? 'success' : 'error' }}">
                                    {{ $tutorial->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Descripción</label>
                        <div class="g_valor_ver"
                            style="min-height: 120px; align-items: flex-start; padding-top: 10px; white-space: pre-line;">
                            {{ $tutorial->descripcion ?: 'Sin descripción registrada.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Lateral: Multimedia e Info -->
        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-image"></i> Miniatura</h4>
                @if ($tutorial->miniatura)
                    <div style="border: 1px solid #eee; padding: 5px; border-radius: 8px; background: #fff;">
                        <img src="{{ $tutorial->miniatura->url ?? asset($tutorial->miniatura->path) }}"
                            style="width: 100%; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    </div>
                @else
                    <div class="g_vacio_pequeno" style="padding: 30px;">
                        <i class="fa-solid fa-image-slash" style="font-size: 2.5rem; color: #eee; margin-bottom: 15px;"></i>
                        <p style="color: #bbb;">Sin miniatura</p>
                    </div>
                @endif
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-chart-line"></i> Estadísticas</h4>
                <div class="g_valor_ver" style="justify-content: space-between; margin-bottom: 5px;">
                    <span class="g_texto_secundario">Visualizaciones:</span>
                    <span class="g_badge primary">{{ $tutorial->clicks }}</span>
                </div>
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-clock"></i> Registro</h4>
                <div class="g_texto_secundario" style="font-size: 0.85rem;">
                    <p class="g_margin_bottom_5"><strong>Creado:</strong>
                        {{ $tutorial->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Actualizado:</strong> {{ $tutorial->updated_at->format('d/m/Y H:i') }}</p>
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