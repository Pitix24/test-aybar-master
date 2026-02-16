<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalles del Evento: {{ $evento->nombre }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @can('entrega-fest.editar')
                <a href="{{ route('erp.entrega-fest.vista.editar', $evento->id) }}" class="g_boton primary">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Información del Evento</h4>
                <div class="g_fila">
                    <div class="g_columna_6">
                        <p class="g_texto_secundario">Código: <span class="g_negrita"
                                style="color: var(--color-primary);">#{{ $evento->codigo }}</span></p>
                        <p class="g_texto_secundario">Fecha: <span
                                class="g_negrita">{{ $evento->fecha_entrega->format('d/m/Y') }}</span></p>
                        <p class="g_texto_secundario">Unidad Negocio: <span
                                class="g_negrita">{{ $evento->unidadNegocio->nombre }}</span></p>
                    </div>
                    <div class="g_columna_6">
                        <p class="g_texto_secundario">Proyecto: <span
                                class="g_negrita">{{ $evento->proyecto->nombre ?? 'N/A' }}</span></p>
                        <p class="g_texto_secundario">Cliente Responsable: <span
                                class="g_negrita">{{ $evento->cliente->nombre_completo }}</span></p>
                        <p class="g_texto_secundario">Estado: <span
                                class="g_badge {{ $evento->activo ? 'success' : 'error' }}">{{ $evento->activo ? 'Activo' : 'Inactivo' }}</span>
                        </p>
                    </div>
                </div>
                <div class="g_margin_top_20">
                    <p class="g_texto_secundario">Descripción:</p>
                    <div class="g_vacio_pequeno" style="text-align: left; background: #f9f9f9; border: 1px solid #eee;">
                        {{ $evento->descripcion ?: 'Sin descripción adicional.' }}
                    </div>
                </div>
            </div>

            <!-- Resumen de Participación -->
            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo">Resumen de Participación</h4>
                <div class="g_fila">
                    <div class="g_columna_4">
                        <div class="g_tarjeta_indicador primary">
                            <i class="fa-solid fa-users-viewfinder"></i>
                            <div>
                                <span>{{ $evento->prospectos->count() }}</span>
                                <label>Prospectos</label>
                            </div>
                        </div>
                    </div>
                    <div class="g_columna_4">
                        <div class="g_tarjeta_indicador success">
                            <i class="fa-solid fa-user-check"></i>
                            <div>
                                <span>{{ $evento->invitados->count() }}</span>
                                <label>Invitados</label>
                            </div>
                        </div>
                    </div>
                    <div class="g_columna_4">
                        <div class="g_tarjeta_indicador dark">
                            <i class="fa-solid fa-users"></i>
                            <div>
                                <span>{{ $evento->invitados->where('confirmado', true)->count() }}</span>
                                <label>Confirmados</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_4">
            <div class="g_panel">
                <h4 class="g_panel_titulo">Auditoría</h4>
                <div class="g_texto_secundario">
                    <p class="g_margin_bottom_10"><strong>Registrado por:</strong><br>{{ $evento->user->name }}</p>
                    <p class="g_margin_bottom_10"><strong>Fecha
                            Creación:</strong><br>{{ $evento->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Última Actualización:</strong><br>{{ $evento->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <div class="g_panel g_margin_top_20">
                <h4 class="g_panel_titulo">Acciones Rápidas</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('erp.prospecto-entrega-fest.vista.crear', $evento->id) }}" class="g_boton light"
                        style="width: 100%;">
                        <i class="fa-solid fa-user-plus"></i> Añadir Prospecto
                    </a>
                    <a href="{{ route('erp.asistencia-entrega-fest.vista.todo', ['entrega_fest_id' => $evento->id]) }}"
                        class="g_boton dark" style="width: 100%;">
                        <i class="fa-solid fa-qrcode"></i> Ir a Control QR
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>