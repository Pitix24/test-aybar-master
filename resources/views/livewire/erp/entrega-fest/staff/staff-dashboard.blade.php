<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Centro de Control Staff
            <span>{{ $evento->codigo }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.panel', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Volver al Panel
            </a>
            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    {{-- HERO --}}
    <div class="g_dashboard_hero">
        <div class="g_dashboard_hero_decor_1"></div>
        <div class="g_dashboard_hero_decor_2"></div>
        <div class="g_dashboard_hero_content">
            <div style="display:flex; justify-content:center; margin-bottom:15px;">
                <span class="g_badge info">Centro Operativo Activo</span>
            </div>
            <h1 class="g_dashboard_hero_title">
                <i class="fa-solid fa-shield-halved"></i> <span>Operaciones:</span> {{ $evento->nombre }}
            </h1>
            <p class="g_dashboard_hero_text">
                Gestión operativa del staff en tiempo real. Run of Show, logística de proveedores, reporte de
                incidencias y control de accesos.
            </p>
        </div>
    </div>

    {{-- ACCESOS OPERATIVOS --}}
    <div class="g_panel_dashboard_grid">

        {{-- Itinerario --}}
        <a href="{{ route('erp.entrega-fest.vista.itinerario', $evento->id) }}"
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

        {{-- Manual de OP --}}
        <a href="{{ route('erp.entrega-fest.vista.mop', $evento->id) }}"
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

        {{-- Incidencias --}}
        <a href="{{ route('erp.entrega-fest.vista.incidencias', $evento->id) }}"
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

        {{-- Proveedores --}}
        <a href="{{ route('erp.entrega-fest.vista.proveedores', $evento->id) }}"
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

        {{-- Asistencia --}}
        <a href="{{ route('erp.entrega-fest.vista.asistencia', $evento->id) }}"
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

        {{-- Recursos --}}
        <a href="{{ route('erp.entrega-fest.vista.recursos', $evento->id) }}"
            class="g_panel g_panel_navegacion g_panel_border_primary">
            <div class="g_panel_navegacion_header">
                <div>
                    <h4 class="g_panel_titulo">Recursos</h4>
                    <p class="g_panel_parrafo">Planos y Protocolos</p>
                </div>
                <i class="fa-solid fa-file-shield"
                    style="opacity:0.5; font-size:2rem; color:var(--color-primario);"></i>
            </div>
            <div class="g_panel_navegacion_cuerpo">
                <div style="display:flex; align-items:baseline; gap:10px;">
                    <span class="g_negrita" style="font-size:2.5rem; line-height:1;"><i
                            class="fa-solid fa-folder-open"></i></span>
                    <span class="g_badge light">Documentación</span>
                </div>
                <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                    <span class="g_trend_valor">
                        <i class="fa-solid fa-book-medical"></i> Contingencias y Seguridad
                    </span>
                </div>
            </div>
            <div class="g_panel_navegacion_footer">
                <span class="g_badge dark">Abrir Biblioteca <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

    </div>

</div>

</div>