<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Área</h2>

        <div class="cabecera_titulo_botones">
            @can('area.lista')
                <a href="{{ route('erp.area.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
            @endcan

            @can('area.editar')
                <a href="{{ route('erp.area.vista.editar', $area_model->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
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
                        <div class="g_margin_top_5">
                            @if ($area_model->activo)
                                <span class="g_badge success">Activo</span>
                            @else
                                <span class="g_badge danger">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre del Área</label>
                            <input type="text" value="{{ $area_model->nombre }}" readonly disabled>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Email Buzón (ATC)</label>
                            <input type="email" value="{{ $area_model->email_buzon ?? '-' }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Color Representativo</label>
                            <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                                <div
                                    style="width: 30px; height: 30px; border-radius: 4px; background-color: {{ $area_model->color }}; border: 1px solid #ddd;">
                                </div>
                                <span>{{ $area_model->color }}</span>
                            </div>
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Icono</label>
                            <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                                <i class="{{ $area_model->icono }}" style="font-size: 20px;"></i>
                                <span>{{ $area_model->icono }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Sedes vinculadas</label>
                        <div class="g_fila">
                            @forelse ($area_model->sedes as $sede)
                                <div class="g_columna_4">
                                    <span class="g_badge light"
                                        style="display: block; margin-bottom: 5px;">{{ $sede->nombre }}</span>
                                </div>
                            @empty
                                <div class="g_columna_12">
                                    <p class="leyenda">No hay sedes vinculadas.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>