<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>{{ $reglamento->titulo }}</h2>

        <div class="cabecera_titulo_botones">
            @can('reglamento.lista')
            <a href="{{ route('erp.reglamento.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            @can('reglamento.editar')
            <a href="{{ route('erp.reglamento.vista.editar', $reglamento->id) }}" class="g_boton primary">
                Editar <i class="fa-solid fa-pencil"></i>
            </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_panel">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información</h4>

                    <div class="g_margin_bottom_10">
                        <label class="label_readonly">Proyecto</label>
                        <div class="g_badge outline primary">
                            {{ $reglamento->proyecto->nombre ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label class="label_readonly">Descripción</label>
                        <p>{{ $reglamento->descripcion ?? 'Sin descripción' }}</p>
                    </div>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label class="label_readonly">Orden</label>
                            <p>{{ $reglamento->orden }}</p>
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label class="label_readonly">Clicks</label>
                            <span class="g_badge primary">{{ $reglamento->clicks }}</span>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label class="label_readonly">Estado</label>
                        <span class="g_badge {{ $reglamento->activo ? 'success' : 'error' }}">
                            {{ $reglamento->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Archivo PDF</h4>

                    @if ($reglamento->archivoPdf)
                    <div class="g_margin_bottom_10" style="text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 10px; color: #d32f2f;">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <p class="g_negrita">{{ $reglamento->archivoPdf->nombre_original }}</p>
                        <p class="g_texto_secundario">
                            {{ number_format($reglamento->archivoPdf->size / 1024 / 1024, 2) }} MB
                        </p>
                        <a href="{{ $reglamento->archivoPdf->url }}" target="_blank" class="g_boton primary"
                            style="margin-top: 10px;">
                            <i class="fa-solid fa-download"></i> Descargar PDF
                        </a>
                    </div>
                    @else
                    <div class="g_alerta info">
                        <i class="fa-solid fa-circle-info"></i>
                        <p>No hay PDF adjunto</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="g_margin_bottom_10">
            <label class="label_readonly">Fechas</label>
            <p>
                <strong>Creado:</strong> {{ $reglamento->created_at->format('d/m/Y H:i') }}<br>
                <strong>Actualizado:</strong> {{ $reglamento->updated_at->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</div>