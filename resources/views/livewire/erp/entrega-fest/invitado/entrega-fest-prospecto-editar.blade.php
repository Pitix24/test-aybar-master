<div class="g_gap_pagina" x-data="{ activeTab: 'prospecto' }">
    <x-loading-overlay wire:loading message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Evaluación de Prospecto</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Gestión
            </a>

            <button type="button" class="g_boton danger" onclick="confirmarEliminarCanal()">
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
                    </div>
                </div>

                <div x-show="activeTab === 'prospecto'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.invitado.partials._tab-datos-basicos')
                </div>

                <div x-show="activeTab === 'backoffice'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.invitado.partials._tab-backoffice')
                </div>

                <div x-show="activeTab === 'legal'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.invitado.partials._tab-legal')
                </div>

                <div x-show="activeTab === 'copropietarios'" x-transition class="g_tab_content">
                    @include('livewire.erp.entrega-fest.invitado.partials._tab-copropietarios')
                </div>

            </div>
        </div>
    </div>
</div>