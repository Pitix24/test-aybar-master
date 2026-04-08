<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle del Estado: <span>{{ $estado_model->nombre }}</span></h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.estado-cliente.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.estado-cliente.editar', $estado_model->id) }}" class="g_boton editar">
                Editar <i class="fa-solid fa-pencil"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 3rem; margin-bottom: 20px;">
                        <i class="fa-solid fa-circle" style="color: {{ $estado_model->color }};"></i>
                    </div>
                    <h3 class="g_negrita">{{ $estado_model->nombre }}</h3>
                    <div style="margin-top: 15px;">
                        @if($estado_model->activo)
                            <span class="g_badge success">Estado Activo</span>
                        @else
                            <span class="g_badge danger">Estado Inactivo</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_negrita g_margin_bottom_20">Información General</h4>
                
                <div class="informacion_resumen_grid">
                    <div class="informacion_resumen_item">
                        <span class="informacion_resumen_label">ID Único</span>
                        <span class="informacion_resumen_valor">{{ $estado_model->id }}</span>
                    </div>
                    <div class="informacion_resumen_item">
                        <span class="informacion_resumen_label">Nombre</span>
                        <span class="informacion_resumen_valor">{{ $estado_model->nombre }}</span>
                    </div>
                    <div class="informacion_resumen_item">
                        <span class="informacion_resumen_label">Color Hex</span>
                        <span class="informacion_resumen_valor">{{ strtoupper($estado_model->color) }}</span>
                    </div>
                    <div class="informacion_resumen_item">
                        <span class="informacion_resumen_label">Fecha Registro</span>
                        <span class="informacion_resumen_valor">{{ $estado_model->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="informacion_resumen_item">
                        <span class="informacion_resumen_label">Última Actualización</span>
                        <span class="informacion_resumen_valor">{{ $estado_model->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="g_margin_top_20">
                    <div class="g_panel_informativo" style="border-left: 5px solid {{ $estado_model->color }};">
                        <p><strong>Vista previa en tablas:</strong></p>
                        <div style="margin-top: 10px;">
                            <span class="g_badge g_badge_soft" style="color: {{ $estado_model->color }};">
                                {{ $estado_model->nombre }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
