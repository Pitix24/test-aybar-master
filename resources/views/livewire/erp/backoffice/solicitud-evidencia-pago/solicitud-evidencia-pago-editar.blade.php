<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, enviarSlin, cerrarManual, enviarCorreo, seleccionarEvidencia"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Solicitud de Evidencia #{{ $solicitud->id }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.solicitud-evidencia-pago.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

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
                    <form wire:submit="update" class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Empresa</label>
                                <select wire:model.live="unidad_negocio_id"
                                    class="@error('unidad_negocio_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($unidades_negocios as $un)
                                        <option value="{{ $un->id }}">{{ $un->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('unidad_negocio_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Proyecto</label>
                                <select wire:model.live="proyecto_id"
                                    class="@error('proyecto_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($proyectos as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Gestor Asignado</label>
                                <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($gestores as $ge)
                                        <option value="{{ $ge->id }}">{{ $ge->name }}</option>
                                    @endforeach
                                </select>
                                @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Estado Actual</label>
                                <select wire:model.live="estado_id" class="@error('estado_id') input-error @enderror">
                                    <option value="">Seleccione...</option>
                                    @foreach($estados as $es)
                                        <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('estado_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="formulario_botones">
                            @can('solicitud-evidencia-pago.editar')
                                <button type="submit" class="g_boton guardar">
                                    <i class="fa-solid fa-save"></i> Guardar Cambios
                                </button>
                            @endcan
                        </div>
                    </form>
                </div>

                <div x-show="activeTab === 'cliente'" x-transition class="g_tab_content">
                    <div class="formulario">  
                         <div class="g_margin_bottom_10">
                            @can('cliente.consultar')
                                <a href="{{ route('erp.cliente.vista.consultar', $solicitud->userCliente->perfilCliente->dni) }}" class="g_boton primary">
                                    <i class="fa-solid fa-border-all"></i> Estado cuenta
                                </a>
                            @endcan
    
                            @can('cliente.ver')
                                <a href="{{ route('erp.cliente.vista.ver', $solicitud->userCliente->id) }}" class="g_boton info">
                                    <i class="fa-solid fa-circle-user"></i> Perfil
                                </a>
                            @endcan
                        </div>               
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

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Fecha vencimiento</label>
                                <input type="text" disabled value="{{ $solicitud->fecha_vencimiento ?? 'Sin asignar' }}">
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
                                        @if(isset($solicitud->monto_operacion) && $evidencia->monto != $solicitud->monto_operacion)
                                            <i class="fa-solid fa-triangle-exclamation" style="color: red;"
                                                title="El monto no coincide con el ERP"></i>
                                        @endif
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
                'evidenciaId' => $evidenciaSeleccionadaId
            ])
        </div>

        <div class="g_columna_4 g_gap_pagina g_columna_invertir">
            <div class="g_panel">
                @if (session('info'))
                    <div class="g_alerta info g_margin_bottom_10">
                        <i class="fa-solid fa-circle-info"></i> {{ session('info') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="g_alerta success g_margin_bottom_10">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

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

                        <div class="g_evidencia_meta_list">
                            <div class="g_evidencia_meta_item">
                                <span class="g_evidencia_meta_label">Lote</span>
                                <span class="g_evidencia_meta_value">{{ $solicitud->lote_completo ?? '—' }}</span>
                            </div>
                            <div class="g_evidencia_meta_item">
                                <span class="g_evidencia_meta_label">Cliente</span>
                                <span class="g_evidencia_meta_value">{{ $solicitud->codigo_cliente ?? '—' }}</span>
                            </div>
                            <div class="g_evidencia_meta_item">
                                <span class="g_evidencia_meta_label">ID Transacción</span>
                                <span class="g_evidencia_meta_value">{{ $solicitud->transaccion_id ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="formulario">
                            @if ($solicitud->slin_asbanc)
                                @if ($solicitud->fecha_validacion && $solicitud->slin_evidencia)
                                    <div class="g_resaltado_caja success">
                                        <span class="g_resaltado_caja_titulo">Validación Digital EXITOSA</span>
                                        <p><strong>Fecha:</strong> {{ $solicitud->fecha_validacion->format('d/m/Y H:i') }}</p>
                                        <p><strong>Respuesta:</strong> {{ $solicitud->slin_respuesta }}</p>
                                    </div>
                                @else
                                    <div class="formulario_botones">
                                        @can('solicitud-evidencia-pago.validar')
                                            <button wire:click="enviarSlin" class="g_boton guardar" style="width: 100%;"
                                                wire:loading.attr="disabled" wire:target="enviarSlin">
                                                <span wire:loading.remove wire:target="enviarSlin">
                                                    VALIDAR CON SLIN <i class="fa-solid fa-paper-plane"></i>
                                                </span>
                                                <span wire:loading wire:target="enviarSlin">
                                                    Enviando a Slin... <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        @endcan
                                    </div>
                                @endif
                            @else
                                @if ($solicitud->fecha_validacion)
                                    <div class="g_resaltado_caja info">
                                        <span class="g_resaltado_caja_titulo">Validación MANUAL</span>
                                        <p><strong>Aprobado el:</strong> {{ $solicitud->fecha_validacion->format('d/m/Y H:i') }}</p>
                                    </div>
                                @else
                                    <div class="formulario_botones">
                                        @can('solicitud-evidencia-pago.validar')
                                            <button wire:click="cerrarManual" class="g_boton guardar" style="width: 100%;"
                                                wire:loading.attr="disabled" wire:target="cerrarManual">
                                                <span wire:loading.remove wire:target="cerrarManual">
                                                    CIERRE MANUAL <i class="fa-solid fa-lock"></i>
                                                </span>
                                                <span wire:loading wire:target="cerrarManual">
                                                    Procesando... <i class="fa-solid fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        @endcan
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @else
                <div class="g_vacio" style="height: 300px;">
                    <i class="fa-solid fa-hand-pointer fa-bounce" style="font-size: 30px"></i>
                    <p>Seleccione una evidencia de la lista para gestionarla.</p>
                </div>
                @endif
            </div>
        </div>
        @livewire('erp.backoffice.solicitud-evidencia-pago.solicitud-evidencia-chat', ['solicitud' => $solicitud])
    </div>
</div>