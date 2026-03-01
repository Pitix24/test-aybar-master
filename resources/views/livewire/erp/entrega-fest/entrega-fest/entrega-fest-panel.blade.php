<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->codigo }}</span>
            Panel de Gestión
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.editar', $evento->id) }}" class="g_boton primary">
                <i class="fa-solid fa-pencil"></i> Editar Evento
            </a>
            <a href="{{ route('erp.entrega-fest.vista.todo') }}" class="g_boton light">
                <i class="fa-solid fa-list"></i> Lista
            </a>
        </div>
    </div>

    {{-- HERO --}}
    <div class="g_dashboard_hero">
        <div class="g_dashboard_hero_decor_1"></div>
        <div class="g_dashboard_hero_decor_2"></div>
        <div class="g_dashboard_hero_content">
            <h1 class="g_dashboard_hero_title">
                <i class="fa-solid fa-calendar-star"></i>
                <span>{{ $evento->nombre }}</span>
            </h1>
            <p class="g_dashboard_hero_text">
                @if($evento->fecha_entrega)
                    <i class="fa-solid fa-calendar-day"></i> {{ $evento->fecha_entrega->format('d/m/Y') }}
                    &nbsp;&bull;&nbsp;
                @endif
                @if($evento->gestor)
                    <i class="fa-solid fa-user-tie"></i> {{ $evento->gestor->name }}
                    &nbsp;&bull;&nbsp;
                @endif
                <span class="g_badge {{ $evento->activo ? 'success' : 'danger' }}" style="font-size:11px;">
                    {{ $evento->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </p>
        </div>
    </div>

    {{-- CONTADORES --}}
    <div class="g_panel_dashboard_grid">

        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-vivo);">
            <div class="g_panel_dashboard_valor">
                <h2>Prospectos</h2>
                <p class="g_negrita">{{ $totalProspectos }}</p>
                <div class="g_panel_dashboard_trend">
                    <span class="g_trend_valor g_trend_up">
                        <i class="fa-solid fa-circle-check"></i> {{ $aprobados }} aprobados
                    </span>
                </div>
            </div>
            <i class="fa-solid fa-users-viewfinder" style="color: var(--color-vivo);"></i>
        </div>

        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-info);">
            <div class="g_panel_dashboard_valor">
                <h2>Invitados</h2>
                <p class="g_negrita">{{ $totalInvitados }}</p>
                <div class="g_panel_dashboard_trend">
                    <span class="g_trend_valor g_trend_up">
                        <i class="fa-solid fa-circle-check"></i> {{ $confirmados }} confirmados
                    </span>
                </div>
            </div>
            <i class="fa-solid fa-users" style="color: var(--color-info);"></i>
        </div>

        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-success);">
            <div class="g_panel_dashboard_valor">
                <h2>Asistentes</h2>
                <p class="g_negrita">{{ $asistentes }}</p>
                <div class="g_panel_dashboard_trend">
                    <span class="g_trend_valor">
                        <i class="fa-solid fa-door-open"></i> Estado: confirmado
                    </span>
                </div>
            </div>
            <i class="fa-solid fa-user-check" style="color: var(--color-success);"></i>
        </div>

        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-danger);">
            <div class="g_panel_dashboard_valor">
                <h2>Incidencias Abiertas</h2>
                <p class="g_negrita">{{ $totalIncidencias }}</p>
                <div class="g_panel_dashboard_trend">
                    <span class="g_trend_valor {{ $totalIncidencias > 0 ? 'g_trend_down' : 'g_trend_up' }}">
                        <i class="fa-solid {{ $totalIncidencias > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check' }}"></i>
                        {{ $totalIncidencias > 0 ? 'Requiere atención' : 'Todo bajo control' }}
                    </span>
                </div>
            </div>
            <i class="fa-solid fa-triangle-exclamation" style="color: var(--color-danger);"></i>
        </div>

    </div>

    {{-- NAVEGACIÓN DE MÓDULOS --}}
    <div class="g_panel_dashboard_grid">

        {{-- Prospectos --}}
        <a href="{{ route('erp.entrega-fest.vista.prospectos', $evento->id) }}" class="g_panel" style="text-decoration:none; cursor:pointer; border-left:4px solid var(--color-vivo);">
            <h4 class="g_panel_titulo">
                <i class="fa-solid fa-users-viewfinder" style="color:var(--color-vivo);"></i> Prospectos
            </h4>
            <p class="g_panel_parrafo">Gestión, evaluación y seguimiento de los prospectos del evento.</p>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                <span class="g_negrita" style="color:var(--color-vivo); font-size:1.3rem;">{{ $totalProspectos }}</span>
                <span class="g_badge primary">Ver <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

        {{-- Invitados --}}
        <a href="{{ route('erp.entrega-fest.vista.invitados', $evento->id) }}" class="g_panel" style="text-decoration:none; cursor:pointer; border-left:4px solid var(--color-info);">
            <h4 class="g_panel_titulo">
                <i class="fa-solid fa-users" style="color:var(--color-info);"></i> Invitados
            </h4>
            <p class="g_panel_parrafo">Generación y control de invitaciones para titulares y copropietarios.</p>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                <span class="g_negrita" style="color:var(--color-info); font-size:1.3rem;">{{ $totalInvitados }}</span>
                <span class="g_badge info">Ver <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

        {{-- Asistencia --}}
        <a href="{{ route('erp.entrega-fest.vista.asistencia', $evento->id) }}" class="g_panel" style="text-decoration:none; cursor:pointer; border-left:4px solid var(--color-success);">
            <h4 class="g_panel_titulo">
                <i class="fa-solid fa-qrcode" style="color:var(--color-success);"></i> Asistencia
            </h4>
            <p class="g_panel_parrafo">Escaneo de QR y registro de entrada de invitados al evento.</p>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                <span class="g_negrita" style="color:var(--color-success); font-size:1.3rem;">{{ $asistentes }}</span>
                <span class="g_badge success">Abrir <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

        {{-- Panel Staff --}}
        <a href="{{ route('erp.entrega-fest.staff.dashboard', $evento->id) }}" class="g_panel" style="text-decoration:none; cursor:pointer; border-left:4px solid var(--color-oscuro);">
            <h4 class="g_panel_titulo">
                <i class="fa-solid fa-shield-halved" style="color:var(--color-oscuro);"></i> Panel Staff
            </h4>
            <p class="g_panel_parrafo">Centro de control operativo: itinerario, MOP, incidencias y proveedores.</p>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                @if($totalIncidencias > 0)
                    <span class="g_badge danger" style="font-size:11px;">{{ $totalIncidencias }} incidencias</span>
                @else
                    <span class="g_badge light" style="font-size:11px;">Operativo</span>
                @endif
                <span class="g_badge dark">Abrir <i class="fa-solid fa-arrow-right"></i></span>
            </div>
        </a>

    </div>

    {{-- DESCRIPCIÓN DEL EVENTO --}}
    @if($evento->descripcion)
        <div class="g_panel">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-circle-info"></i> Descripción del Evento</h4>
            <p class="g_panel_parrafo" style="font-size:14px; line-height:1.6;">{{ $evento->descripcion }}</p>
        </div>
    @endif

</div>