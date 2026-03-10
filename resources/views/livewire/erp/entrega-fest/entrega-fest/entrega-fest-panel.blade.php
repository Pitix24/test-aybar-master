<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Panel de Gestión del Evento
            <span>{{ $evento->codigo }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.lista')
                <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            @can('entrega-fest.editar')
                <a href="{{ route('erp.entrega-fest.vista.editar', $evento->id) }}" class="g_boton primary">
                    Editar Evento <i class="fa-solid fa-pencil"></i>
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
            <span class="g_badge {{ $evento->activo ? 'success' : 'danger' }}">
                {{ $evento->activo ? 'Activo' : 'Inactivo' }}
            </span>
            <p class="g_dashboard_hero_text">
                @if($evento->fecha_entrega)
                    <i class="fa-solid fa-calendar-day"></i> {{ $evento->fecha_entrega->format('d/m/Y') }}
                    &nbsp;&bull;&nbsp;
                @endif
                @if($evento->gestor)
                    <i class="fa-solid fa-user-tie"></i> {{ $evento->gestor->name }}
                @endif
            </p>
            <h1 class="g_dashboard_hero_title">
                <span>{{ $evento->nombre }}</span>
            </h1>
            <p class="g_dashboard_hero_text">
                {{ $evento->descripcion }}
            </p>
        </div>
    </div>

    <div class="g_panel_dashboard_grid">
        @can('prospecto.navegacion')
            <a href="{{ route('erp.entrega-fest.prospecto.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_info">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Prospectos</h4>
                        <p class="g_panel_parrafo">Evaluación y Seguimiento</p>
                    </div>
                    <i class="fa-solid fa-users-viewfinder" style="opacity:0.5; font-size:2rem;"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;">{{ $totalProspectos }}</span>
                        <span class="g_badge warning">Total captados</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor g_trend_up">
                            <i class="fa-solid fa-circle-check"></i> {{ $aprobados }} aprobados para el evento
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge warning">Gestionar <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('invitado.navegacion')
            <a href="{{ route('erp.entrega-fest.invitado.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_success">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Invitados</h4>
                        <p class="g_panel_parrafo">Control de Invitaciones</p>
                    </div>
                    <i class="fa-solid fa-envelope-open-text" style=" opacity:0.5; font-size:2rem;"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;">{{ $totalInvitados }}</span>
                        <span class="g_badge success">Confirmados: {{ $confirmados }}</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-clock"></i> Pendientes de confirmación:
                            {{ $totalInvitados - $confirmados }}
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge success">Ver Lista <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('asistencia.navegacion')
            <a href="{{ route('erp.entrega-fest.asistencia.todo', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_active">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Asistencia</h4>
                        <p class="g_panel_parrafo">Control de Puerta (QR)</p>
                    </div>
                    <i class="fa-solid fa-qrcode" style="opacity:0.5; font-size:2rem;"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;">{{ $asistentes }}</span>
                        <span class="g_badge info">Ingresos hoy</span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor">
                            <i class="fa-solid fa-door-open"></i> Escaneo activo en tiempo real
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge info">Abrir Lector <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan

        @can('entrega-fest.ver-staff')
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}"
                class="g_panel g_panel_navegacion g_panel_border_primary">
                <div class="g_panel_navegacion_header">
                    <div>
                        <h4 class="g_panel_titulo">Panel Staff</h4>
                        <p class="g_panel_parrafo">Centro Operativo</p>
                    </div>
                    <i class="fa-solid fa-shield-halved" style="opacity:0.5; font-size:2rem;"></i>
                </div>
                <div class="g_panel_navegacion_cuerpo">
                    <div style="display:flex; align-items:baseline; gap:10px;">
                        <span class="g_negrita" style="font-size:2.5rem; line-height:1;">
                            @if($totalIncidencias > 0)
                                {{ $totalIncidencias }}
                            @else
                                <i class="fa-solid fa-check-circle"></i>
                            @endif
                        </span>
                        <span class="g_badge {{ $totalIncidencias > 0 ? 'danger' : 'light' }}">
                            {{ $totalIncidencias > 0 ? 'Incidencias' : 'Staff OK' }}
                        </span>
                    </div>
                    <div class="g_panel_dashboard_trend" style="margin-top:15px;">
                        <span class="g_trend_valor {{ $totalIncidencias > 0 ? 'g_trend_down' : 'g_trend_up' }}">
                            {{ $totalIncidencias > 0 ? 'Requiere atención inmediata' : 'Sin problemas operativos' }}
                        </span>
                    </div>
                </div>
                <div class="g_panel_navegacion_footer">
                    <span class="g_badge danger">Ir a Operaciones <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            </a>
        @endcan
    </div>
</div>