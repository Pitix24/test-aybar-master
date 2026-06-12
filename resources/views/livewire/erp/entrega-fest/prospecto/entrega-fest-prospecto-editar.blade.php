<div class="g_gap_pagina" x-data="{ activeTab: 'prospecto' }">
    <x-loading-overlay wire:loading
        wire:target="updateProspecto, updateReubicacion, updateBackoffice, updateBackofficeSupervisor, updateLegal, updateLegalSupervisor, updateLlamada, solicitarEliminarProspecto, eliminarProspectoOn"
        message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Evaluación de Prospecto</h2>

        <div class="cabecera_titulo_botones">
            @can('prospecto.lista')
            <a href="{{ route('erp.entrega-fest.prospecto.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>
            @endcan

            @can('entrega-fest.ver-panel')
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>
            @endcan
            @can('prospecto.eliminar')
            <button type="button" class="g_boton danger" wire:click="solicitarEliminarProspecto">
                Eliminar <i class="fa-solid fa-trash-can"></i>
            </button>
            @endcan

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
                                {{ substr($prospecto->nombres, 0, 1) }}
                            </div>
                        </div>
                    </div>
                    <div class="g_perfil_avatar_info">
                        <h3 class="g_negrita">{{ $prospecto->nombres }}</h3>
                        <p>{{ $prospecto->dni }}</p>
                    </div>
                </div>

                <div class="g_perfil_politicas">
                    <div class="informacion_resumen_grid">
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Evento</span>
                            <span class="informacion_resumen_valor">{{ $evento->nombre }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Proyecto</span>
                            <span class="informacion_resumen_valor">{{ $prospecto->proyecto?->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Fecha Registro</span>
                            <span class="informacion_resumen_valor">{{ $prospecto->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="informacion_resumen_item">
                            <span class="informacion_resumen_label">Registrado por</span>
                            <span class="informacion_resumen_valor">{{ $prospecto->user?->name ?? 'Sistema' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="informacion_beneficio_item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ $prospecto->email }}</span>
                    </div>
                    <div class="informacion_beneficio_item" style="margin-top: 8px;">
                        <i class="fa-solid fa-phone"></i>
                        <span>{{ $prospecto->celular }}</span>
                    </div>
                </div>

                <!--  LINK DE PREINVITACION E INVITACION AL PROPIETARIO --->
                <div class="g_panel_seccion_divider"></div>
                <div class="g_gap_small">
                    <h4 class="g_negrita g_texto_centrado"
                        style="margin-bottom: 12px; font-size: 0.9rem; text-transform: uppercase; color: var(--g-texto-suave);">
                        Enlaces del Propietario</h4>

                    <div class="g_gap_small">
                        <label class="g_label" style="font-size: 0.75rem;">Link Pre-Invitación</label>
                        <div class="g_input_grupo" x-data="{ copied: false }">
                            <input type="text" class="g_input small" value="{{ $link_preinvitacion }}" readonly>
                            <button type="button" class="g_boton_icono info small"
                                @click="navigator.clipboard.writeText('{{ $link_preinvitacion }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                title="Copiar enlace">
                                <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                            </button>
                            <a href="{{ $link_preinvitacion }}" target="_blank" class="g_boton_icono dark small"
                                title="Ver enlace">
                                <i class="fa-solid fa-external-link"></i>
                            </a>
                        </div>
                    </div>

                    <div class="g_gap_small" style="margin-top: 10px;">
                        <label class="g_label" style="font-size: 0.75rem;">Link Invitación Asistencia</label>
                        <div class="g_input_grupo" x-data="{ copied: false }">
                            <input type="text" class="g_input small" value="{{ $link_invitacion }}" readonly>
                            <button type="button" class="g_boton_icono info small"
                                @click="navigator.clipboard.writeText('{{ $link_invitacion }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                title="Copiar enlace">
                                <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                            </button>
                            <a href="{{ $link_invitacion }}" target="_blank" class="g_boton_icono dark small"
                                title="Ver enlace">
                                <i class="fa-solid fa-external-link"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @if($prospecto->estado_contrato_preeliminar_emitido === 'CONFORME')
                <div class="g_gap_small" style="margin-top: 10px;">
                    <label class="g_label" style="font-size: 0.75rem;">
                        Link Cita de Contrato
                        @if(!$prospecto->fecha_firma)
                            <span class="g_badge light" style="font-size: 0.65rem; margin-left: 5px;">Pendiente de agendar</span>
                        @else
                            <span class="g_badge success" style="font-size: 0.65rem; margin-left: 5px; background:#8e44ad; color:#fff;">
                                Agendada: {{ \Carbon\Carbon::parse($prospecto->fecha_firma)->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </label>
                    <div class="g_input_grupo" x-data="{ copied: false }">
                        <input type="text" class="g_input small" value="{{ $link_cita_contrato }}" readonly>
                        <button type="button" class="g_boton_icono info small"
                            @click="navigator.clipboard.writeText('{{ $link_cita_contrato }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            title="Copiar enlace">
                            <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                        </button>
                        <a href="{{ $link_cita_contrato }}" target="_blank" class="g_boton_icono dark small"
                            title="Ver enlace">
                            <i class="fa-solid fa-external-link"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="g_columna_8">
            <div class="g_panel">
                <div class="g_tab_navegacion">
                    <div class="g_tab_botones">
                        <button @click="activeTab = 'prospecto'" class="g_tab_boton"
                            :class="activeTab === 'prospecto' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-address-card"></i> Datos Básicos
                        </button>
                        <button @click="activeTab = 'backoffice'" class="g_tab_boton"
                            :class="activeTab === 'backoffice' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-briefcase"></i> BackOffice
                        </button>
                        <button @click="activeTab = 'legal'" class="g_tab_boton"
                            :class="activeTab === 'legal' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-file-contract"></i> Información Legal
                        </button>
                        <button @click="activeTab = 'copropietarios'" class="g_tab_boton"
                            :class="activeTab === 'copropietarios' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-people-group"></i> Copropietarios
                            @if(count($copropietarios) > 0)
                            <span class="g_badge_circular info" style="margin-left:6px;">
                                {{ count($copropietarios) }}
                            </span>
                            @endif
                        </button>
                        <button @click="activeTab = 'llamada'" class="g_tab_boton"
                            :class="activeTab === 'llamada' ? 'g_tab_active' : 'g_tab_inactive'">
                            <i class="fa-solid fa-phone"></i> Llamada
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'prospecto'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.prospecto.partials._tab-datos-basicos')

                    @livewire('erp.entrega-fest.prospecto.entrega-fest-prospecto-bancarizacion', ['prospectoId' =>
                    $prospecto->id])
                </div>

                <div x-show="activeTab === 'backoffice'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.prospecto.partials._tab-backoffice')
                </div>

                <div x-show="activeTab === 'legal'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.prospecto.partials._tab-legal')
                </div>

                <div x-show="activeTab === 'copropietarios'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.prospecto.partials._tab-copropietarios')
                </div>

                <div x-show="activeTab === 'llamada'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.prospecto.partials._tab-llamada')
                </div>
            </div>
        </div>
    </div>
</div>
