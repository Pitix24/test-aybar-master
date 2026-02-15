<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Ver Unidad de Negocio</h2>

        <div class="cabecera_titulo_botones">
            @can('unidad-negocio.ver')
                <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('unidad-negocio.editar')
                <a href="{{ route('erp.unidad-negocio.vista.editar', $unidad_model->id) }}" class="g_boton primary">
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

                    <div class="g_fila">
                        <div class="g_columna_12 g_margin_bottom_10">
                            <label>Estado</label>
                            <br>
                            @if ($unidad_model->activo)
                                <span class="g_badge success">Activo</span>
                            @else
                                <span class="g_badge danger">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombre Comercial</label>
                            <input type="text" value="{{ $unidad_model->nombre }}" readonly disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Razón Social</label>
                            <input type="text" value="{{ $unidad_model->razon_social }}" readonly disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>RUC</label>
                            <input type="text" value="{{ $unidad_model->ruc ?? '-' }}" readonly disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>SLIN ID</label>
                            <input type="text" value="{{ $unidad_model->slin_id ?? '-' }}" readonly disabled>
                        </div>
                    </div>

                    <h4 class="g_panel_titulo g_margin_top_20">Representante (CAVALI)</h4>
                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Tipo Documento</label>
                            <input type="text" value="{{ $unidad_model->cavali_girador_tipo_documento ?? '-' }}"
                                readonly disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nº Documento</label>
                            <input type="text" value="{{ $unidad_model->cavali_girador_documento ?? '-' }}" readonly
                                disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Nombres</label>
                            <input type="text" value="{{ $unidad_model->cavali_girador_nombre ?? '-' }}" readonly
                                disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Apellidos</label>
                            <input type="text" value="{{ $unidad_model->cavali_girador_apellido ?? '-' }}" readonly
                                disabled>
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Correo Electrónico</label>
                            <input type="text" value="{{ $unidad_model->cavali_girador_email ?? '-' }}" readonly
                                disabled>
                        </div>
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label>Teléfono</label>
                            <input type="text" value="{{ $unidad_model->cavali_girador_telefono ?? '-' }}" readonly
                                disabled>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="button" class="g_boton dark" onclick="history.back()">
                            <i class="fa-solid fa-arrow-left"></i> Regresar
                        </button>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Auditoría</h4>
                    <div class="g_margin_bottom_10">
                        <label>ID de Registro</label>
                        <p class="leyenda">#{{ $unidad_model->id }}</p>
                    </div>
                    <div class="g_margin_bottom_10">
                        <label>Fecha de Creación</label>
                        <p class="leyenda">
                            {{ $unidad_model->created_at ? $unidad_model->created_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                    <div>
                        <label>Última Actualización</label>
                        <p class="leyenda">
                            {{ $unidad_model->updated_at ? $unidad_model->updated_at->format('d/m/Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>