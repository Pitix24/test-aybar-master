<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->codigo }}</span>
            Centro de Control Staff
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.ver', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Volver al Evento
            </a>
        </div>
    </div>

    {{-- HERO --}}
    <div class="g_dashboard_hero">
        <div class="g_dashboard_hero_decor_1"></div>
        <div class="g_dashboard_hero_decor_2"></div>
        <div class="g_dashboard_hero_content">
            <h1 class="g_dashboard_hero_title">
                <i class="fa-solid fa-shield-halved"></i>
                Operaciones: <span>{{ $evento->nombre }}</span>
            </h1>
            <p class="g_dashboard_hero_text">
                Panel operativo del staff. Gestiona el itinerario, las tareas, los proveedores y las incidencias en
                tiempo real.
            </p>
        </div>
    </div>

    {{-- CONTADORES --}}
    <div class="g_panel_dashboard_grid">
        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-vivo);">
            <div class="g_panel_dashboard_valor">
                <h2>Bloques Itinerario</h2>
                <p class="g_negrita">{{ $evento->itinerario_bloques_count }}</p>
            </div>
            <i class="fa-solid fa-clock" style="color: var(--color-vivo);"></i>
        </div>
        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-danger);">
            <div class="g_panel_dashboard_valor">
                <h2>Incidencias Abiertas</h2>
                <p class="g_negrita">{{ $evento->incidencias_count }}</p>
            </div>
            <i class="fa-solid fa-triangle-exclamation" style="color: var(--color-danger);"></i>
        </div>
        <div class="g_panel g_panel_dashboard" style="border-left-color: var(--color-success);">
            <div class="g_panel_dashboard_valor">
                <h2>Proveedores</h2>
                <p class="g_negrita">{{ $evento->proveedores_count }}</p>
            </div>
            <i class="fa-solid fa-truck" style="color: var(--color-success);"></i>
        </div>
    </div>

    {{-- ACCESOS DIRECTOS --}}
    <div class="g_panel_dashboard_grid">

        <a href="{{ route('erp.entrega-fest.staff.itinerario', $evento->id) }}" class="g_panel"
            style="text-decoration:none; cursor:pointer; border-left: 4px solid var(--color-info);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-clock" style="color:var(--color-info);"></i> Itinerario
            </h4>
            <p class="g_panel_parrafo">Run of Show, tiempos y cronograma en vivo del evento.</p>
            <span class="g_badge info">Abrir <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a href="{{ route('erp.entrega-fest.staff.mop', $evento->id) }}" class="g_panel"
            style="text-decoration:none; cursor:pointer; border-left: 4px solid var(--color-vivo);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-list-check" style="color:var(--color-vivo);"></i> Manual de
                OP</h4>
            <p class="g_panel_parrafo">Mis tareas asignadas por fase y responsabilidad.</p>
            <span class="g_badge primary">Abrir <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a href="{{ route('erp.entrega-fest.staff.incidencias', $evento->id) }}" class="g_panel"
            style="text-decoration:none; cursor:pointer; border-left: 4px solid var(--color-danger);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-triangle-exclamation"
                    style="color:var(--color-danger);"></i> Incidencias</h4>
            <p class="g_panel_parrafo">Reportar problemas, emergencias o fallas del evento.</p>
            <span class="g_badge danger">Abrir <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a href="{{ route('erp.entrega-fest.staff.proveedores', $evento->id) }}" class="g_panel"
            style="text-decoration:none; cursor:pointer; border-left: 4px solid var(--color-success);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-truck-loading" style="color:var(--color-success);"></i>
                Proveedores</h4>
            <p class="g_panel_parrafo">Logística de entrada, montaje y horarios de servicios.</p>
            <span class="g_badge success">Abrir <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a href="{{ route('erp.entrega-fest.vista.asistencia', $evento->id) }}" class="g_panel"
            style="text-decoration:none; cursor:pointer; border-left: 4px solid var(--color-warning);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-qrcode" style="color:var(--color-warning);"></i> Asistencia
            </h4>
            <p class="g_panel_parrafo">Acceso rápido a escaneo y check-in de invitados.</p>
            <span class="g_badge warning">Abrir <i class="fa-solid fa-arrow-right"></i></span>
        </a>

        <a href="{{ route('erp.entrega-fest.staff.recursos', $evento->id) }}" class="g_panel"
            style="text-decoration:none; cursor:pointer; border-left: 4px solid var(--color-secundario);">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-file-lines" style="color:var(--color-secundario);"></i>
                Recursos</h4>
            <p class="g_panel_parrafo">Planos, protocolos y planes de contingencia del evento.</p>
            <span class="g_badge" style="background:var(--color-secundario); color:var(--color-primario);">Abrir <i
                    class="fa-solid fa-arrow-right"></i></span>
        </a>

    </div>

</div>