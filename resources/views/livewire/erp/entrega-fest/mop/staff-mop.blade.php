<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Mi Manual de Operaciones
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.mop')
                <a href="{{ route('erp.entrega-fest.mop.tareas', $evento->id) }}" class="g_boton info">
                    Gestionar MOP <i class="fa-solid fa-list-check"></i>
                </a>
                <a href="{{ route('erp.entrega-fest.mop-plantilla.todo') }}" class="g_boton light">
                    Plantillas <i class="fa-solid fa-paste"></i>
                </a>
                <a href="{{ route('erp.entrega-fest.mop.tareas.crear', $evento->id) }}" class="g_boton guardar">
                    <i class="fa-solid fa-plus"></i> Crear Tarea
                </a>
            @endcan

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton dark">
                <i class="fa-solid fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>

    {{-- TABS DE FASE --}}
    <div class="g_panel">
        <div class="g_tab_navegacion">
            <div class="g_tab_botones">
                <button wire:click="$set('fase', 'ANTES')"
                    class="g_tab_boton {{ $fase === 'ANTES' ? 'g_tab_active' : 'g_tab_inactive' }}">
                    <i class="fa-solid fa-hourglass-start"></i> Antes del Evento
                </button>
                <button wire:click="$set('fase', 'DURANTE')"
                    class="g_tab_boton {{ $fase === 'DURANTE' ? 'g_tab_active' : 'g_tab_inactive' }}">
                    <i class="fa-solid fa-bolt"></i> Durante el Evento
                </button>
                <button wire:click="$set('fase', 'CIERRE')"
                    class="g_tab_boton {{ $fase === 'CIERRE' ? 'g_tab_active' : 'g_tab_inactive' }}">
                    <i class="fa-solid fa-flag-checkered"></i> Cierre
                </button>
            </div>
        </div>

        <div class="g_tab_content g_gap_pagina" style="gap:6px; margin-top:10px;">
            @forelse($tareas as $tarea)
                <div class="g_panel"
                    style="border-left: 4px solid {{ $tarea->esta_completado ? 'var(--color-success)' : 'var(--borde-card-color, #e5e7eb)' }}; padding:12px 15px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <button wire:click="toggleTarea({{ $tarea->id }})"
                            class="g_boton {{ $tarea->esta_completado ? 'success' : 'light' }}"
                            style="width:32px; height:32px; padding:0; min-width:32px; border-radius:50%; flex-shrink:0;">
                            <i class="fa-solid {{ $tarea->esta_completado ? 'fa-check' : 'fa-circle' }}"
                                style="font-size:11px;"></i>
                        </button>
                        <div style="flex:1;">
                            <p class="g_negrita"
                                style="{{ $tarea->esta_completado ? 'text-decoration:line-through; opacity:0.5;' : '' }} margin:0 0 2px 0;">
                                {{ $tarea->titulo }}
                            </p>
                            @if($tarea->instruccion)
                                <p class="g_inferior" style="margin:0;">{{ $tarea->instruccion }}</p>
                            @endif
                        </div>
                        @if($tarea->esta_completado)
                            <span class="g_badge success" style="font-size:11px;"><i class="fa-solid fa-check"></i> Listo</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="g_alerta info">
                    <i class="fa-solid fa-circle-info"></i>
                    No tienes tareas pendientes para esta fase.
                </div>
            @endforelse
        </div>
    </div>

    {{-- INFO BOX --}}
    <div class="g_resaltado_caja info">
        <span class="g_resaltado_caja_titulo"><i class="fa-solid fa-circle-info"></i> ¿Cómo funciona?</span>
        Este manual contiene tus responsabilidades específicas. Márcalas conforme las cumplas — los coordinadores verán
        tu progreso en tiempo real.
    </div>

</div>