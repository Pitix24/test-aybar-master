<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Avance de Proyecto</h2>

        <div class="cabecera_titulo_botones">
            @can('avance-proyecto.lista')
                <a href="{{ route('erp.avance-proyecto.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('avance-proyecto.editar')
                <a href="{{ route('erp.avance-proyecto.vista.editar', $item->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Visualización del Video</h4>
                
                <div class="g_video_contenedor" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; background: #000;">
                    <iframe 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
                        src="https://www.youtube.com/embed/{{ $item->video_id }}" 
                        title="YouTube video player" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                </div>

                @if($item->miniatura)
                    <div class="g_margin_top_20">
                        <h4 class="g_panel_titulo">Miniatura</h4>
                        <img src="{{ $item->miniatura->url }}" alt="Miniatura" style="max-width: 300px; border-radius: 8px;">
                    </div>
                @endif
                
                <div class="g_margin_top_20">
                    <h3 class="g_negrita">{{ $item->titulo }}</h3>
                    <p class="g_texto_secundario" style="white-space: pre-line;">{{ $item->descripcion }}</p>
                </div>
            </div>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Información de Seguimiento</h4>
                
                <div class="g_detalle_lista">
                    <div class="item">
                        <span class="label">Estado</span>
                        <span class="g_badge {{ $item->activo ? 'success' : 'error' }}">
                            {{ $item->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="item">
                        <span class="label">Orden</span>
                        <span class="value">{{ $item->orden }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Clicks</span>
                        <span class="g_badge primary">{{ $item->clicks }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Fecha Creación</span>
                        <span class="value">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="g_panel">
                <h4 class="g_panel_titulo">Jerarquía Aplicada</h4>
                <div class="g_detalle_lista">
                    <div class="item">
                        <span class="label">Unidad</span>
                        <span class="value">{{ $item->unidadNegocio->nombre }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Grupo</span>
                        <span class="value">{{ $item->grupoProyecto->nombre ?? 'N/A (General)' }}</span>
                    </div>
                    <div class="item">
                        <span class="label">Proyecto</span>
                        <span class="value">{{ $item->proyecto->nombre ?? 'N/A (General)' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
