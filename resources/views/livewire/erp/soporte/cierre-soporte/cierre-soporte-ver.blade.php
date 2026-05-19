<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle de Cierre de Soporte</h2>

        <div class="cabecera_titulo_botones">
            @can('cierre-soporte.vista-lista')
            <a href="{{ route('erp.cierre-soporte.lista') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('cierre-soporte.vista-editar')
            <a href="{{ route('soporte.supervisor', $cierre->id) }}" class="g_boton primary">
                Editar <i class="fa-solid fa-pencil"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Información General</h4>

                <div class="g_fila">
                    <div class="g_columna_6">
                        <div class="g_campo_lectura">
                            <label>ID</label>
                            <p>{{ $cierre->id }}</p>
                        </div>
                    </div>

                    <div class="g_columna_6">
                        <div class="g_campo_lectura">
                            <label>Estado</label>
                            <p>
                                @if($cierre->activo)
                                <span class="g_badge success">Activo</span>
                                @else
                                <span class="g_badge danger">Inactivo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="g_campo_lectura">
                    <label>Nombre</label>
                    <p>{{ $cierre->nombre }}</p>
                </div>

                <div class="g_fila">
                    <div class="g_columna_6">
                        <div class="g_campo_lectura">
                            <label>Color</label>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div
                                    style="width: 30px; height: 30px; background-color: {{ $cierre->color ?? '#64748b' }}; border-radius: 4px; border: 1px solid #e5e7eb;">
                                </div>
                                <span class="g_badge g_badge_soft" style="color: {{ $cierre->color }};">{{
                                    strtoupper($cierre->color) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="g_columna_6">
                        <div class="g_campo_lectura">
                            <label>Icono</label>
                            <p>
                                <i class="{{ $cierre->icono ?? 'fa-solid fa-circle' }}"
                                    style="color: {{ $cierre->color ?? '#64748b' }}; margin-right: 8px;"></i>
                                {{ $cierre->icono }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="g_fila">
                    <div class="g_columna_6">
                        <div class="g_campo_lectura">
                            <label>Creado</label>
                            <p>{{ $cierre->created_at ? $cierre->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="g_columna_6">
                        <div class="g_campo_lectura">
                            <label>Actualizado</label>
                            <p>{{ $cierre->updated_at ? $cierre->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
