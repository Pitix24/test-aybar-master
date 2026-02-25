<div class="g_gap_pagina" x-data="{ activeTab: 'invitacion' }">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalles de Invitación</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel g_gap_pagina">
                <div class="g_perfil_avatar_container">
                    <div class="g_perfil_avatar_wrapper">
                        <div class="g_perfil_avatar">
                            <div class="g_perfil_avatar_placeholder">
                                {{ substr($invitado->prospecto->nombres, 0, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="g_perfil_avatar_info">
                        <h3 class="g_negrita">{{ $invitado->prospecto->nombres }}</h3>
                        <p>{{ $invitado->prospecto->dni }}</p>
                    </div>
                </div>

                <div class="g_perfil_politicas">
                    <div class="informacion_resumen_grid">
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Código Invitado</span>
                            <span class="informacion_resumen_valor g_negrita"
                                style="color: var(--color-primary);">{{ $codigo_invitado }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Estado Confirmación</span>
                            <span class="informacion_resumen_valor">
                                @php
                                    $claseConf = match ($estado_confirmacion) {
                                        'pendiente' => 'primary',
                                        'confirmado' => 'success',
                                        'no_asiste' => 'danger',
                                        default => 'light',
                                    };
                                @endphp
                                <span class="g_badge {{ $claseConf }}">{{ strtoupper($estado_confirmacion) }}</span>
                            </span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Evento</span>
                            <span class="informacion_resumen_valor">{{ $evento->nombre }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Proyecto</span>
                            <span
                                class="informacion_resumen_valor">{{ $invitado->prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Registrado por</span>
                            <span
                                class="informacion_resumen_valor">{{ $invitado->prospecto->user?->name ?? 'Sistema' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="informacion_beneficio_item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $invitado->prospecto->email }}</span>
                    </div>
                    <div class="informacion_beneficio_item" style="margin-top: 8px;">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $invitado->prospecto->celular }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button @click="activeTab = 'invitacion'" class="g_tab_boton"
                            :class="activeTab === 'invitacion' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-ticket"></i> Datos Asistencia
                        </button>
                        <button @click="activeTab = 'prospecto'" class="g_tab_boton"
                            :class="activeTab === 'prospecto' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-address-card"></i> Datos Prospecto
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'invitacion'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_6">
                                <label>Estado de Confirmación</label>
                                <input type="text" value="{{ strtoupper($estado_confirmacion) }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_15 g_columna_6">
                                <label>Código de Invitado</label>
                                <input type="text" value="{{ $codigo_invitado }}" class="g_input_disabled" disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_6">
                                <label>Acompañantes Registrados</label>
                                <input type="text" value="{{ $cantidad_acompanantes_permitidos }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_15 g_columna_6">
                                <label>Transporte Seleccionado</label>
                                @php
                                    $transporteTexto = match ($transporte) {
                                        'bus' => 'BUS AYBAR',
                                        'propio' => 'MOVILIDAD PROPIA',
                                        'na' => 'NO APLICA',
                                        default => $transporte,
                                    };
                                @endphp
                                <input type="text" value="{{ strtoupper($transporteTexto) }}" class="g_input_disabled"
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_15 g_columna_12">
                                <label>Observaciones de Asistencia (Llenado por cliente)</label>
                                <textarea class="g_input_disabled" disabled
                                    rows="3">{{ $observaciones_asistencia ?? 'Sin observaciones' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'prospecto'" x-transition class="g_tab_content">
                    <div class="formulario">
                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>Nombres Completos</label>
                                <input type="text" value="{{ $invitado->prospecto->nombres }}" class="g_input_disabled"
                                    disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>DNI / Documento</label>
                                <input type="text" value="{{ $invitado->prospecto->dni }}" class="g_input_disabled"
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Proyecto</label>
                                <input type="text" value="{{ $invitado->prospecto->proyecto?->nombre ?? 'N/A' }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Manzana</label>
                                <input type="text" value="{{ $invitado->prospecto->manzana }}" class="g_input_disabled"
                                    disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Lote</label>
                                <input type="text" value="{{ $invitado->prospecto->lote }}" class="g_input_disabled"
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Grupo</label>
                                <input type="text" value="GRUPO {{ $invitado->prospecto->grupo }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Estado BackOffice</label>
                                <input type="text" value="{{ strtoupper($invitado->prospecto->estado_backoffice) }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Fecha Firma Contrato</label>
                                <input type="text"
                                    value="{{ $invitado->prospecto->fecha_firma ? date('d/m/Y', strtotime($invitado->prospecto->fecha_firma)) : 'N/A' }}"
                                    class="g_input_disabled" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>