<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Centro de Control Staff
            <span>{{ $evento->codigo }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.ver-panel')
                <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton info">
                    <i class="fa-solid fa-grip"></i> Panel de Gestión
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_dashboard_hero">
        <div class="g_dashboard_hero_decor_1"></div>
        <div class="g_dashboard_hero_decor_2"></div>
        <div class="g_dashboard_hero_content">
            <h1 class="g_dashboard_hero_title">
                <i class="fa-solid fa-shield-halved"></i> <span>Operaciones:</span> {{ $evento->nombre }}
            </h1>
            <p class="g_dashboard_hero_text">
                Gestión operativa del staff en tiempo real. Run of Show, logística de proveedores, reporte de
                incidencias y control de accesos.
            </p>
        </div>
    </div>

    <div class="g_panel_dashboard_grid">
        @can('itinerario.navegacion')
            <a href="{{ route('erp.entrega-fest.itinerario.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_vivo">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Itinerario</h4>
                        <p class="g_panel_parrafo">Run of Show & Tiempos</p>
                    </div>
                    <i class="fa-solid fa-clock" style="opacity:0.5; font-size:2rem; color:var(--color-vivo);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita"
                            style="font-size:2.5rem; line-height:1;">{{ $evento->itinerario_bloques_count }}</span>
                        <span class="g_badge primary">Bloques hoy</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor g_trend_up">
                            <i class="fa-solid fa-play"></i> Seguimiento en vivo
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge primary">Abrir Cronograma <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('mop.navegacion')
            <a href="{{ route('erp.entrega-fest.mop.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_info">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Manual de OP</h4>
                        <p class="g_panel_parrafo">Tareas y Responsabilidades</p>
                    </div>
                    <i class="fa-solid fa-list-check" style="opacity:0.5; font-size:2rem; color:var(--color-info);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;"><i
                                class="fa-solid fa-clipboard-list"></i></span>
                        <span class="g_badge info">Mis Tareas</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-circle-info"></i> Revisa tus asignaciones
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge info">Ver Manual <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('incidencia.navegacion')
            <a href="{{ route('erp.entrega-fest.incidencia.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_danger">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Incidencias</h4>
                        <p class="g_panel_parrafo">Reportes Operativos</p>
                    </div>
                    <i class="fa-solid fa-triangle-exclamation"
                        style="opacity:0.5; font-size:2rem; color:var(--color-danger);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita"
                            style="font-size:2.5rem; line-height:1;">{{ $evento->incidencias_count }}</span>
                        <span class="g_badge {{ $evento->incidencias_count > 0 ? 'danger' : 'success' }}">
                            {{ $evento->incidencias_count > 0 ? 'Abiertas' : 'Sin errores' }}
                        </span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor {{ $evento->incidencias_count > 0 ? 'g_trend_down' : 'g_trend_up' }}">
                            <i class="fa-solid fa-shield"></i>
                            {{ $evento->incidencias_count > 0 ? 'Atención inmediata' : 'Operación segura' }}
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge danger">Gestionar <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('proveedor.navegacion')
            <a href="{{ route('erp.entrega-fest.proveedor.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_success">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Proveedores</h4>
                        <p class="g_panel_parrafo">Logística y Servicios</p>
                    </div>
                    <i class="fa-solid fa-truck-fast" style="opacity:0.5; font-size:2rem; color:var(--color-success);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita"
                            style="font-size:2.5rem; line-height:1;">{{ $evento->proveedores_count }}</span>
                        <span class="g_badge success">En servicio</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-dolly"></i> Montaje y Coordinación
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge success">Ver Logística <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('asistencia.navegacion')
            <a href="{{ route('erp.entrega-fest.asistencia.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_warning">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Asistencia</h4>
                        <p class="g_panel_parrafo">Check-in Invitados</p>
                    </div>
                    <i class="fa-solid fa-qrcode" style="opacity:0.5; font-size:2rem; color:var(--color-warning);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;"><i
                                class="fa-solid fa-camera"></i></span>
                        <span class="g_badge warning">Acceso Rápido</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-user-check"></i> Escaneo de QR activado
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge warning">Escaneo QR <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('recurso.navegacion')
            <a href="{{ route('erp.entrega-fest.recurso.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_primary">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Recursos</h4>
                        <p class="g_panel_parrafo">Planos y Manuales</p>
                    </div>
                    <i class="fa-solid fa-map" style="opacity:0.5; font-size:2rem; color:var(--color-primario);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;"><i
                                class="fa-solid fa-folder-open"></i></span>
                        <span class="g_badge light">Logística</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-file-pdf"></i> Documentación de apoyo
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge dark">Ver Mapas <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('protocolo.navegacion')
            <a href="{{ route('erp.entrega-fest.protocolo.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_vivo">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Protocolos</h4>
                        <p class="g_panel_parrafo">Guiones y Discursos</p>
                    </div>
                    <i class="fa-solid fa-scroll" style="opacity:0.5; font-size:2rem; color:var(--color-vivo);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;"><i
                                class="fa-solid fa-feather"></i></span>
                        <span class="g_badge secondary">Protocolo</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-microphone"></i> Material para locución
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge primary">Ver Discursos <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('contingencia.navegacion')
            <a href="{{ route('erp.entrega-fest.contingencia.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_danger">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Contingencia</h4>
                        <p class="g_panel_parrafo">Seguridad y Riesgos</p>
                    </div>
                    <i class="fa-solid fa-shield-halved"
                        style="opacity:0.5; font-size:2rem; color:var(--color-danger);"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;"><i
                                class="fa-solid fa-biohazard"></i></span>
                        <span class="g_badge danger">Crítico</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor g_trend_down">
                            <i class="fa-solid fa-triangle-exclamation"></i> Planes de respuesta
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge danger">Ver Planes <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan
    </div>
</div>