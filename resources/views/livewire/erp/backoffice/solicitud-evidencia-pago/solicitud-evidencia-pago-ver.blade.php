<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalle Solicitud de Evidencia #{{ $solicitud->id }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.solicitud-evidencia-pago.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            @can('solicitud-evidencia-pago.editar')
                <a href="{{ route('erp.solicitud-evidencia-pago.vista.editar', $solicitud->id) }}" class="g_boton guardar">
                    Editar <i class="fa-solid fa-pencil"></i>
                </a>
            @endcan

            <button type="button" class="g_boton info" wire:click="$dispatch('toggleChat')">
                Chat <i class="fa-solid fa-comments"></i>
            </button>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_8 g_gap_pagina">
            <div class="g_panel" x-data="{ activeTab: 'general' }">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-circle-info"></i> Gestión General
                        </button>

                        <button type="button" @click="activeTab = 'cliente'"
                            :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-user-tag"></i> Datos Cliente
                        </button>

                        <button type="button" @click="activeTab = 'cuota'"
                            :class="activeTab === 'cuota' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Detalle Cuota
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'general'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Empresa</label>
                                <select disabled>
                                    <option value="">{{ $solicitud->unidadNegocio->nombre ?? '—' }}</option>
                                </select>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Proyecto</label>
                                <select disabled>
                                    <option value="">{{ $solicitud->proyecto->nombre ?? '—' }}</option>
                                </select>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Gestor Asignado</label>
                                <select disabled>
                                    <option value="">{{ $solicitud->gestor->name ?? '—' }}</option>
                                </select>
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Estado Actual</label>
                                <select disabled>
                                    <option value="">{{ $solicitud->estado->nombre ?? '—' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombre del Cliente</label>
                                <input type="text" disabled value="{{ $solicitud->userCliente->name ?? '—' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Documento (DNI/RUC)</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->userCliente->perfilCliente->dni ?? '—' }}">
                            </div>
                        </div>
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Email principal</label>
                                <input type="text" disabled value="{{ $solicitud->userCliente->email ?? '—' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Celular</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->userCliente->perfilCliente->telefono_principal ?? '—' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'cuota'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Razón social</label>
                                <input type="text" disabled value="{{ $solicitud->razon_social ?? '—' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Proyecto</label>
                                <input type="text" disabled value="{{ $solicitud->nombre_proyecto ?? '—' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Etapa</label>
                                <input type="text" disabled value="{{ $solicitud->etapa ?? '—' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Manzana</label>
                                <input type="text" disabled value="{{ $solicitud->manzana ?? '—' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Código Cliente</label>
                                <input type="text" disabled value="{{ $solicitud->codigo_cliente ?? '—' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Lote</label>
                                <input type="text" disabled value="{{ $solicitud->lote ?? '—' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Código Cuota</label>
                                <input type="text" disabled value="{{ $solicitud->codigo_cuota ?? '—' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>N° Cuota</label>
                                <input type="text" disabled value="{{ $solicitud->numero_cuota ?? '—' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Monto</label>
                                <input type="text" disabled value="S/ {{ number_format($solicitud->slin_monto, 2) }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Penalidad</label>
                                <input type="text" disabled
                                    value="S/ {{ number_format($solicitud->slin_penalidad, 2) }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Comprobante</label>
                                <input type="text" disabled value="{{ $solicitud->comprobante ?? '—' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Ticket</label>
                                <input type="text" disabled value="{{ $solicitud->ticket ?? '—' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-code-compare"></i> Comparador de Transacciones</h4>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Origen</th>
                                <th style="width: 150px;">Fecha Operación</th>
                                <th>N° Operación</th>
                                <th style="width: 150px;">Monto</th>
                                <th style="width: 180px;">Estado ERP</th>
                                <th style="width: 80px;" class="g_celda_centro">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="g_fila_seleccionada">
                                <td class="g_negrita g_resaltar">ERP</td>
                                <td>{{ $solicitud->fecha_operacion ?? '—' }}</td>
                                <td class="g_negrita">{{ $solicitud->slin_numero_operacion ?? '—' }}</td>
                                <td class="g_negrita">
                                    S/ {{ number_format($solicitud->monto_operacion ?? 0, 2) }}
                                </td>
                                <td>
                                    <span
                                        class="g_badge {{ $solicitud->slin_asbanc ? 'success' : 'danger' }}">
                                        {{ $solicitud->slin_asbanc ? 'ES ASBANC' : 'NO ASBANC' }}
                                    </span>
                                </td>
                                <td class="g_celda_centro">
                                    <i class="fa-solid fa-anchor" title="Dato de referencia"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="g_comparador_separador">
                    <span>Evidencias Recibidas del Cliente</span>
                </div>

                <div class="g_contenedor_tabla">
                    <table class="g_tabla">
                        <thead>
                            <tr>
                                <th style="width: 80px;" class="g_celda_centro">Ref.</th>
                                <th style="width: 150px;">Fecha Evidencia</th>
                                <th>N° Operación</th>
                                <th style="width: 150px;">Monto</th>
                                <th style="width: 180px;">Estado</th>
                                <th style="width: 80px;" class="g_celda_centro">Comparar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitud->evidencias as $evidencia)
                                <tr class="{{ $evidenciaSeleccionadaId == $evidencia->id ? 'g_fila_seleccionada' : '' }}">
                                    <td class="g_celda_centro">
                                        <span class="g_badge light">#{{ $loop->iteration }}</span>
                                    </td>
                                    <td>{{ $evidencia->fecha ?? '—' }}</td>
                                    <td>{{ $evidencia->numero_operacion ?? '—' }}</td>
                                    <td class="g_negrita">
                                        S/ {{ number_format($evidencia->monto, 2) }}
                                    </td>
                                    <td>
                                        @if ($evidencia->estado)
                                            <span class="g_badge g_badge_soft" style="color: {{ $evidencia->estado->color }}">
                                                <i class="{{ $evidencia->estado->icono }}"></i>
                                                {{ $evidencia->estado->nombre }}
                                            </span>
                                        @else
                                            <span class="g_badge light">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="g_celda_centro">
                                        <div class="g_comparador_acciones">
                                            <button wire:click="seleccionarEvidencia({{ $evidencia->id }})"
                                                class="g_accion ver {{ $evidenciaSeleccionadaId == $evidencia->id ? 'active' : '' }}"
                                                title="Seleccionar para comparar">
                                                <i class="fa-solid fa-magnifying-glass-chart"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @livewire('erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-email', [
                'solicitud' => $solicitud,
                'evidenciaId' => $evidenciaSeleccionadaId,
                'soloLectura' => true
            ])
        </div>

        <div class="g_columna_4 g_gap_pagina g_columna_invertir">
            <div class="g_panel">
                <h4 class="g_panel_titulo"><i class="fa-solid fa-magnifying-glass"></i> Evidencia seleccionada</h4>

                @if ($evidenciaSeleccionada)
                    <div class="g_evidencia_visor_panel">
                        <div class="g_evidencia_previa">
                            <a href="{{ $evidenciaSeleccionada->url }}" target="_blank" title="Ver original">
                                <img src="{{ $evidenciaSeleccionada->url }}" alt="Comprobante de Pago">
                            </a>
                        </div>

                        <div class="g_margin_bottom_10">
                            <span class="g_badge {{ $solicitud->slin_asbanc ? 'success' : 'light' }}">
                                <i class="fa-solid fa-building-columns"></i> Asbanc: {{ $solicitud->slin_asbanc ? 'SÍ' : 'NO' }}
                            </span>

                            <span class="g_badge {{ $solicitud->slin_evidencia ? 'primary' : 'light' }}">
                                <i class="fa-solid {{ $solicitud->slin_evidencia ? 'fa-check-double' : 'fa-hourglass-half' }}"></i> 
                                Validado: {{ $solicitud->slin_evidencia ? 'SÍ' : 'NO' }}
                            </span>
                        </div>

                        @if ($solicitud->fecha_validacion)
                            <div class="g_resaltado_caja {{ $solicitud->slin_evidencia ? 'success' : 'info' }}">
                                <span class="g_resaltado_caja_titulo">Validación {{ $solicitud->slin_evidencia ? 'Digital' : 'Manual' }}</span>
                                <p><strong>Fecha:</strong> {{ $solicitud->fecha_validacion->format('d/m/Y H:i') }}</p>
                                @if($solicitud->slin_respuesta)
                                    <p><strong>Respuesta:</strong> {{ $solicitud->slin_respuesta }}</p>
                                @endif
                                <p><strong>Gestor:</strong> {{ $solicitud->usuarioValida->name ?? 'N/A' }}</p>
                            </div>
                        @else
                            <div class="g_alerta_info">
                                <i class="fa-solid fa-clock"></i> Pendiente de validación.
                            </div>
                        @endif
                    </div>
                @else
                <div class="g_vacio" style="height: 300px;">
                    <i class="fa-solid fa-hand-pointer fa-bounce" style="font-size: 30px"></i>
                    <p>Seleccione una evidencia de la lista para ver el detalle.</p>
                </div>
                @endif
            </div>
        </div>
        @livewire('erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-chat', ['solicitud' => $solicitud, 'soloLectura' => true])
    </div>
</div>
