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
                            <i class="fa-solid fa-file-invoice"></i> General
                        </button>

                        <button type="button" @click="activeTab = 'cliente'"
                            :class="activeTab === 'cliente' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-user"></i> Datos Cliente
                        </button>

                        <button type="button" @click="activeTab = 'cuota'"
                            :class="activeTab === 'cuota' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-file-invoice"></i> Cuota
                        </button>

                        <button type="button" @click="activeTab = 'transaccion'"
                            :class="activeTab === 'transaccion' ? 'g_tab_active' : 'g_tab_inactive'"
                            class="g_tab_boton">
                            <i class="fa-solid fa-file-invoice"></i> Transacción
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
                                <i class="fa-solid fa-save"></i> Guardar cambios básicos
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
                                <input type="text" disabled value="{{ $solicitud->razon_social ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Proyecto</label>
                                <input type="text" disabled value="{{ $solicitud->nombre_proyecto ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Etapa</label>
                                <input type="text" disabled value="{{ $solicitud->etapa ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Manzana</label>
                                <input type="text" disabled value="{{ $solicitud->manzana ?? 'Sin asignar' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Codigo cliente</label>
                                <input type="text" disabled value="{{ $solicitud->codigo_cliente ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Lote</label>
                                <input type="text" disabled value="{{ $solicitud->lote ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Codigo cuota</label>
                                <input type="text" disabled value="{{ $solicitud->codigo_cuota ?? 'Sin asignar' }}">
                            </div>

                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>N° cuota</label>
                                <input type="text" disabled value="{{ $solicitud->numero_cuota ?? 'Sin asignar' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Penalidad</label>
                                <input type="text" disabled value="{{ $solicitud->slin_penalidad ?? 'Sin asignar' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Cromprobante</label>
                                <input type="text" disabled value="{{ $solicitud->comprobante ?? 'Sin asignar' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Ticket</label>
                                <input type="text" disabled value="{{ $solicitud->ticket ?? 'Sin asignar' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Cuota Monto</label>
                                <input type="text" disabled value="{{ $solicitud->slin_monto ?? 'Sin asignar' }}">
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label>Fecha vencimiento</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->fecha_vencimiento ?? 'Sin asignar' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'transaccion'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label class="g_boton g_boton_info">Fecha operación</label>
                                <input type="text" disabled value="{{ $solicitud->fecha_operacion ?? 'Sin asignar' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label class="g_boton g_boton_info">N° Operación</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->slin_numero_operacion ?? 'Sin asignar' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label class="g_boton g_boton_info">Monto operación</label>
                                <input type="text" disabled value="{{ $solicitud->monto_operacion ?? 'Sin asignar' }}">
                            </div>
                            <div class="g_margin_bottom_10 g_columna_3">
                                <label class="g_boton g_boton_success">Asbanc</label>
                                <input type="text" disabled value="{{ $solicitud->slin_asbanc ? 'SI' : 'No' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="formulario">
                <div class="g_panel g_gap_pagina">
                    @foreach ($solicitud->evidencias as $evidencia)
                        <div class="g_panel">
                            <h4 class="g_panel_titulo">Medio de pago recibido ({{ $loop->iteration }})
                                @if ($evidencia->estado)
                                    <span class="g_badge" style="background: {{ $evidencia->estado->color }}">
                                        <i class="{{ $evidencia->estado->icono }}"></i>
                                        {{ $evidencia->estado->nombre }}
                                    </span>
                                @endif
                            </h4>

                            <div class="g_fila">
                                <div class="g_margin_bottom_10 g_columna_3">
                                    <label class="g_boton g_boton_info">Fecha operación</label>
                                    <input type="text" disabled value="{{ $evidencia->fecha ?? 'Sin asignar' }}">
                                </div>

                                <div class="g_margin_bottom_10 g_columna_3">
                                    <label class="g_boton g_boton_info">N° Operación</label>
                                    <input type="text" disabled value="{{ $evidencia->numero_operacion ?? 'Sin asignar' }}">
                                </div>

                                <div class="g_margin_bottom_10 g_columna_3">
                                    <label class="g_boton g_boton_info">Monto</label>
                                    <input type="text" disabled value="{{ $evidencia->monto ?? 'Sin asignar' }}">
                                </div>
                            </div>

                            <div class="formulario_botones">
                                <button wire:click="seleccionarEvidencia({{ $evidencia->id }})"
                                    class="g_boton g_boton_guardar">
                                    Seleccionar evidencia
                                </button>

                                <a href="{{ $evidencia->url }}" target="_blank" class="g_boton g_boton_primary">
                                    Ver <i class="fa-regular fa-file-image fa-xl"></i>
                                </a>

                                <a href="{{ $evidencia->url }}" download class="g_boton g_boton_dark">
                                    Descargar <i class="fa-solid fa-download"></i>
                                </a>
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
                            <i class="fa-solid fa-file-invoice"></i> Enviar
                        </button>

                        <button type="button" @click="activeTab = 'historial'"
                            :class="activeTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                            <i class="fa-solid fa-clock"></i> Historial
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'enviar'" x-transition class="g_tab_content">
                    <div class="g_panel">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-paper-plane"></i> Enviar Notificación /
                            Observación</h4>
                        <div class="formulario">
                            <div class="g_margin_bottom_10">
                                <label>Mensaje para el cliente</label>
                                <textarea wire:model.live="mensaje_correo" rows="6"
                                    placeholder="Escribe el motivo del rechazo u observación..."></textarea>
                                @error('mensaje_correo') <p class="mensaje_error">{{ $message }}</p> @enderror
                            </div>
                            <div class="formulario_botones">
                                <button type="button" wire:click="enviarCorreo" class="g_boton g_boton_primary">
                                    <i class="fa-solid fa-envelope"></i> Enviar Correo
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div x-show="activeTab === 'historial'" x-transition class="g_tab_content">
                    <div class="g_margin_top_20">
                        <h4 class="g_panel_titulo"><i class="fa-solid fa-history"></i> Correos enviados</h4>
                        <div class="g_contenedor_tabla">
                            <table class="g_tabla">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Remitente</th>
                                        <th>Asunto</th>
                                        <th>Mensaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($solicitud->correos as $cor)
                                        <tr>
                                            <td class="g_inferior">{{ $cor->enviado_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $cor->emisor->name ?? 'Sistema' }}</td>
                                            <td class="g_negrita">{{ $cor->asunto }}</td>
                                            <td style="font-size: 0.85rem;">{{ $cor->mensaje }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="g_celda_vacia">No hay registros de correos enviados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_4 g_gap_pagina g_columna_invertir">
            <div class="g_panel">
                @if (session('info'))
                    <div class="g_alerta_info">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('info') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="g_alerta_succes">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <h4 class="g_panel_titulo">Evidencia seleccionada</h4>

                @if ($evidenciaSeleccionada)
                    <div class="g_centrar_elemento g_margin_bottom_20">
                        <a href="{{ $evidenciaSeleccionada->url }}" target="_blank">
                            <img src="{{ $evidenciaSeleccionada->url }}" alt="Comprobante" style="height: 520px;">
                        </a>
                    </div>

                    <div class="g_celda_wrap g_margin_bottom_10">
                        <span class="g_badge {{ $solicitud->slin_asbanc ? 'activo' : '' }}">
                            Asbanc: {{ $solicitud->slin_asbanc ? 'SI' : 'No' }}
                        </span>

                        <span class="g_badge {{ $solicitud->slin_evidencia ? 'activo' : '' }}">
                            Evidencia: {{ $solicitud->slin_evidencia ? 'SI' : 'No' }}
                        </span>
                    </div>

                    <div class="g_panel_parrafo">
                        <p>Lote:{{ $solicitud->lote_completo ?? 'Sin asignar' }}</p>
                        <p>Cliente cod.:{{ $solicitud->codigo_cliente ?? 'Sin asignar' }}</p>
                        <p>ID Cobranza/Transacción:{{ $solicitud->transaccion_id ?? 'Sin asignar' }}</p>
                    </div>

                    @if ($solicitud->slin_asbanc)
                        @if ($solicitud->fecha_validacion && $solicitud->slin_evidencia)
                            <div class="g_margin_bottom_10">
                                <label>Fecha validación</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->fecha_validacion ? $solicitud->fecha_validacion->format('d/m/Y H:i') : 'Falta validar' }}">
                            </div>

                            <div class="g_margin_bottom_10">
                                <label>Respuesta Slin</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->slin_respuesta ? $solicitud->slin_respuesta : 'Falta enviar a Slin' }}">
                            </div>
                        @else
                            <div class="g_margin_bottom_10">
                                <div class="formulario_botones">
                                    <button wire:click="enviarSlin" class="guardar" wire:loading.attr="disabled"
                                        wire:target="enviarSlin">
                                        <span wire:loading.remove wire:target="enviarSlin">Enviar</span>
                                        <span wire:loading wire:target="enviarSlin">Enviando...</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
                        @if ($solicitud->fecha_validacion)
                            <div class="g_margin_bottom_10">
                                <label>Fecha validación</label>
                                <input type="text" disabled
                                    value="{{ $solicitud->fecha_validacion ? $solicitud->fecha_validacion->format('d/m/Y H:i') : 'Falta validar' }}">
                            </div>
                        @else
                            <div class="g_margin_bottom_10">
                                <div class="formulario_botones">
                                    <button wire:click="cerrarManual" class="guardar" wire:loading.attr="disabled"
                                        wire:target="cerrarManual">
                                        <span wire:loading.remove wire:target="cerrarManual">Cerrar de manera
                                            manual</span>
                                        <span wire:loading wire:target="cerrarManual">Enviando...</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endif
                @else
                    <span>Seleccione una evidencia</span>
                @endif
            </div>
        </div>
    </div>