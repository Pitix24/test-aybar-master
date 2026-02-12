<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, enviarSlin, cerrarManual, enviarCorreo, seleccionarEvidencia"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Solicitud de Evidencia #{{ $solicitud->id }}</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.solicitud-evidencia-pago.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
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
                                    <option value="">Seleccionar...</option>
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
                                    <option value="">Seleccionar...</option>
                                    @foreach($proyectos as $pr)
                                        <option value="{{ $pr->id }}">{{ $pr->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Gestor Asignado</label>
                                <select wire:model.live="gestor_id" class="@error('gestor_id') input-error @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($gestores as $ge)
                                        <option value="{{ $ge->id }}">{{ $ge->name }}</option>
                                    @endforeach
                                </select>
                                @error('gestor_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Estado Actual</label>
                                <select wire:model.live="estado_id" class="@error('estado_id') input-error @enderror">
                                    <option value="">Seleccionar...</option>
                                    @foreach($estados as $es)
                                        <option value="{{ $es->id }}">{{ $es->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('estado_id') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="formulario_botones">
                            <button type="submit" class="g_boton g_boton_guardar">
                                <i class="fa-solid fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
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

                <div class="g_comparador_contenedor">
                    <div class="g_comparador_fila g_comparador_header">
                        <div>Origen</div>
                        <div>Fecha Operación</div>
                        <div>N° Operación</div>
                        <div>Monto</div>
                        <div>Estado</div>
                        <div>Acción</div>
                    </div>

                    <div class="g_comparador_fila g_comparador_referencia" title="Datos registrados en el ERP">
                        <div>ERP</div>
                        <div>{{ $solicitud->fecha_operacion ?? '—' }}</div>
                        <div>{{ $solicitud->slin_numero_operacion ?? '—' }}</div>
                        <div>
                            S/ {{ number_format($solicitud->monto_operacion ?? 0, 2) }}
                        </div>
                        <div>
                            <span class="g_badge {{ $solicitud->slin_asbanc ? 'g_badge_success' : 'g_badge_danger' }}">
                                {{ $solicitud->slin_asbanc ? 'ES ASBANC' : 'NO ASBANC' }}
                            </span>
                        </div>
                        <div>
                            <i class="fa-solid fa-anchor" title="Dato de referencia"></i>
                        </div>
                    </div>

                    <div class="g_comparador_separador">
                        <span>Evidencias Recibidas del Cliente</span>
                    </div>

                    @foreach ($solicitud->evidencias as $evidencia)
                        <div
                            class="g_comparador_fila {{ $evidenciaSeleccionadaId == $evidencia->id ? 'g_fila_activa' : '' }}">
                            <div>
                                <span class="g_badge g_badge_light">#{{ $loop->iteration }}</span>
                            </div>
                            <div>{{ $evidencia->fecha ?? '—' }}</div>
                            <div>{{ $evidencia->numero_operacion ?? '—' }}</div>
                            <div>
                                S/ {{ number_format($evidencia->monto, 2) }}
                            </div>
                            <div>
                                @if ($evidencia->estado)
                                    <span class="g_badge g_badge_soft" style="color: {{ $evidencia->estado->color }}">
                                        <i class="{{ $evidencia->estado->icono }}"></i>
                                        {{ $evidencia->estado->nombre }}
                                    </span>
                                @else
                                    <span class="g_badge g_badge_light">Pendiente</span>
                                @endif
                            </div>
                            <div>
                                <div class="g_comparador_acciones">
                                    <button wire:click="seleccionarEvidencia({{ $evidencia->id }})"
                                        class="g_accion_ver {{ $evidenciaSeleccionadaId == $evidencia->id ? 'active' : '' }}"
                                        title="Seleccionar para comparar">
                                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="g_panel" x-data="{ activeTab: 'enviar' }">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button type="button" @click="activeTab = 'enviar'"
                            :class="activeTab === 'enviar' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-paper-plane"></i> Enviar Observación
                        </button>

                        <button type="button" @click="activeTab = 'historial'"
                            :class="activeTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock-rotate-left"></i> Historial de Correos
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'enviar'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_margin_bottom_10">
                            <label>Mensaje para el cliente</label>
                            <textarea wire:model.live="mensaje_correo" rows="6"
                                placeholder="Escribe el motivo del rechazo u observación... El cliente recibirá un correo con estos detalles."></textarea>
                            @error('mensaje_correo') <p class="mensaje_error">{{ $message }}</p> @enderror
                        </div>
                        <div class="formulario_botones">
                            <button type="button" wire:click="enviarCorreo" class="g_boton g_boton_primary">
                                Enviar Correo <i class="fa-solid fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'historial'" x-transition class="g_tab_content">
                    <div class="g_contenedor_tabla">
                        <table class="g_tabla">
                            <thead>
                                <tr>
                                    <th>Fecha Envío</th>
                                    <th>Gestor</th>
                                    <th>Asunto</th>
                                    <th>Mensaje Corto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($solicitud->correos as $cor)
                                    <tr>
                                        <td class="g_negrita g_inferior">{{ $cor->enviado_at->format('d/m H:i') }}</td>
                                        <td><small>{{ $cor->emisor->name ?? 'Sistema' }}</small></td>
                                        <td><span class="g_badge g_badge_light">{{ $cor->asunto }}</span></td>
                                        <td class="g_resumir" title="{{ $cor->mensaje }}">{{ $cor->mensaje }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="g_celda_vacia">No hay registros de correos enviados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_4 g_gap_pagina g_columna_invertir">
            <div class="g_panel">
                @if (session('info'))
                    <div class="g_alerta_info g_margin_bottom_10">
                        <i class="fa-solid fa-circle-info"></i> {{ session('info') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="g_alerta_success g_margin_bottom_10">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <h4 class="g_panel_titulo"><i class="fa-solid fa-magnifying-glass"></i> Evidencia seleccionada</h4>

                @if ($evidenciaSeleccionada)
                    <div class="g_centrar_elemento g_margin_bottom_20"
                        style="background: var(--g_color_light); padding: 10px; border-radius: 12px;">
                        <a href="{{ $evidenciaSeleccionada->url }}" target="_blank" class="g_contenedor_img_zoom">
                            <img src="{{ $evidenciaSeleccionada->url }}" alt="Comprobante"
                                style="max-height: 450px; width: 100%; object-fit: contain; border-radius: 8px; box-shadow: var(--g_shadow);">
                        </a>
                    </div>

                    <div class="g_margin_bottom_10"
                        style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                        <span class="g_badge {{ $solicitud->slin_asbanc ? 'g_badge_success' : 'g_badge_dark' }}">
                            Asbanc: {{ $solicitud->slin_asbanc ? 'SÍ' : 'NO' }}
                        </span>

                        <span class="g_badge {{ $solicitud->slin_evidencia ? 'g_badge_primary' : 'g_badge_light' }}">
                            Validado: {{ $solicitud->slin_evidencia ? 'SÍ' : 'NO' }}
                        </span>
                    </div>

                    <div class="g_panel_parrafo g_inferior"
                        style="background: var(--g_color_soft_primary); padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <p><strong>Lote:</strong> {{ $solicitud->lote_completo ?? '—' }}</p>
                        <p><strong>Cliente:</strong> {{ $solicitud->codigo_cliente ?? '—' }}</p>
                        <p><strong>Transacción:</strong> {{ $solicitud->transaccion_id ?? '—' }}</p>
                    </div>

                    <div class="formulario">
                        @if ($solicitud->slin_asbanc)
                            @if ($solicitud->fecha_validacion && $solicitud->slin_evidencia)
                                <div class="g_margin_bottom_10">
                                    <label>Fecha Validación</label>
                                    <input type="text" disabled value="{{ $solicitud->fecha_validacion->format('d/m/Y H:i') }}">
                                </div>

                                <div class="g_margin_bottom_10">
                                    <label>Respuesta Slin</label>
                                    <textarea disabled rows="2">{{ $solicitud->slin_respuesta }}</textarea>
                                </div>
                            @else
                                <div class="formulario_botones">
                                    <button wire:click="enviarSlin" class="g_boton g_boton_primary" style="width: 100%;"
                                        wire:loading.attr="disabled" wire:target="enviarSlin">
                                        <span wire:loading.remove wire:target="enviarSlin">Validar con Slin <i
                                                class="fa-solid fa-paper-plane"></i></span>
                                        <span wire:loading wire:target="enviarSlin">Enviando... <i
                                                class="fa-solid fa-spinner fa-spin"></i></span>
                                    </button>
                                </div>
                            @endif
                        @else
                            @if ($solicitud->fecha_validacion)
                                <div class="g_margin_bottom_10">
                                    <label>Fecha Validación</label>
                                    <input type="text" disabled value="{{ $solicitud->fecha_validacion->format('d/m/Y H:i') }}">
                                </div>
                            @else
                                <div class="formulario_botones">
                                    <button wire:click="cerrarManual" class="g_boton g_boton_secondary" style="width: 100%;"
                                        wire:loading.attr="disabled" wire:target="cerrarManual">
                                        <span wire:loading.remove wire:target="cerrarManual">Cierre Manual <i
                                                class="fa-solid fa-lock"></i></span>
                                        <span wire:loading wire:target="cerrarManual">Procesando... <i
                                                class="fa-solid fa-spinner fa-spin"></i></span>
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                @else
                    <div class="g_vacio">
                        <i class="fa-solid fa-arrow-left-long fa-bounce"></i>
                        <p>Seleccione una evidencia de la lista para gestionarla.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>