<div class="g_gap_pagina" x-data="{ activeTab: 'invitacion' }">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Detalles de Invitación</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.invitado.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>

            <button type="button" class="g_boton danger" onclick="confirmarEliminarInvitado()">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_fila">
        <div class="g_columna_4">
            <div class="g_panel g_gap_pagina">
                <div class="g_perfil_avatar_container">
                    <div class="g_perfil_avatar_wrapper">
                        <div class="g_perfil_avatar">
                            <div class="g_perfil_avatar_placeholder">
                                {{ substr($invitado->nombre_completo, 0, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="g_perfil_avatar_info">
                        <h3 class="g_negrita">{{ $invitado->nombre_completo }}</h3>
                        <p>{{ $invitado->dni }}</p>
                    </div>
                </div>

                <div
                    style="text-align: center; margin: 15px 0; padding: 15px; background: white; border-radius: 12px; border: 1px solid #eee;">
                    <p class="g_negrita" style="font-size: 0.8rem; margin-bottom: 10px; color: #666;">CÓDIGO QR DE
                        ACCESO</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $codigo_invitado }}"
                        alt="QR Access"
                        style="width: 150px; height: 150px; border: 5px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <div class="g_negrita" style="margin-top: 10px; color: var(--color-primary); letter-spacing: 2px;">
                        {{ $codigo_invitado }}
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
                                class="informacion_resumen_valor">{{ $invitado->prospecto?->proyecto?->nombre ?? $invitado->copropietario?->prospecto?->proyecto?->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Registrado por</span>
                            <span
                                class="informacion_resumen_valor">{{ $invitado->prospecto?->user?->name ?? $invitado->copropietario?->prospecto?->user?->name ?? 'Sistema' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="informacion_beneficio_item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $invitado->email ?? '—' }}</span>
                    </div>
                    <div class="informacion_beneficio_item" style="margin-top: 8px;">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $invitado->celular ?? '—' }}</span>
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
                        <button @click="activeTab = 'acompanantes'" class="g_tab_boton"
                            :class="activeTab === 'acompanantes' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-users"></i> Acompañantes
                            @if(count($acompanantes) > 0)
                                <span class="g_badge_circular info" style="margin-left:6px;">
                                    {{ count($acompanantes) }} / {{ $cantidad_acompanantes_permitidos }}
                                </span>
                            @endif
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
                                <input type="text" value="{{ $invitado->nombre_completo }}" class="g_input_disabled"
                                    disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_6">
                                <label>DNI / Documento</label>
                                <input type="text" value="{{ $invitado->dni }}" class="g_input_disabled"
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Proyecto</label>
                                <input type="text" value="{{ $invitado->prospecto?->proyecto?->nombre ?? $invitado->copropietario?->prospecto?->proyecto?->nombre ?? 'N/A' }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Manzana</label>
                                <input type="text" value="{{ $invitado->manzana }}" class="g_input_disabled"
                                    disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Lote</label>
                                <input type="text" value="{{ $invitado->lote }}" class="g_input_disabled"
                                    disabled>
                            </div>
                        </div>

                        <div class="g_fila">
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Grupo</label>
                                <input type="text" value="GRUPO {{ $invitado->prospecto?->grupo ?? $invitado->copropietario?->prospecto?->grupo ?? 'N/A' }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Estado BackOffice</label>
                                @php
                                    $estadoBo = $invitado->prospecto?->estado_backoffice ?? $invitado->copropietario?->prospecto?->estado_backoffice ?? 'N/A';
                                @endphp
                                <input type="text" value="{{ strtoupper($estadoBo) }}"
                                    class="g_input_disabled" disabled>
                            </div>
                            <div class="g_margin_bottom_10 g_columna_4">
                                <label>Fecha Firma Contrato</label>
                                @php
                                    $fechaFirma = $invitado->prospecto?->fecha_firma ?? $invitado->copropietario?->prospecto?->fecha_firma;
                                @endphp
                                <input type="text"
                                    value="{{ $fechaFirma ? date('d/m/Y', strtotime($fechaFirma)) : 'N/A' }}"
                                    class="g_input_disabled" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="activeTab === 'acompanantes'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.invitado.partials._tab-acompanantes')
                </div>
            </div>
        </div>
    </div>
</div>