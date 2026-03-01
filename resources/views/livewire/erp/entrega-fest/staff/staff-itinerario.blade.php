<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Itinerario en Vivo
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.staff.dashboard', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Panel Staff
            </a>
        </div>
    </div>

    {{-- BLOQUES DE ITINERARIO --}}
    @forelse($evento->itinerarioBloques as $bloque)
        <div class="g_panel"
            style="border-left: 4px solid {{ $bloque->estado === 'COMPLETADO' ? 'var(--color-success)' : ($bloque->estado === 'EN_CURSO' ? 'var(--color-warning)' : 'var(--borde-card-color, #e5e7eb)') }};">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">

                {{-- INFO PRINCIPAL --}}
                <div>
                    <p class="g_inferior g_mayuscula" style="margin:0 0 2px 0;">
                        <i class="fa-solid fa-clock"></i>
                        {{ $bloque->hora_inicio }} — {{ $bloque->hora_fin }}
                        @if($bloque->ubicacion)
                            &nbsp;&bull;&nbsp; <i class="fa-solid fa-location-dot"></i> {{ $bloque->ubicacion }}
                        @endif
                    </p>
                    <h4 class="g_panel_titulo" style="margin:0 0 4px 0;">{{ $bloque->titulo }}</h4>
                    <span class="g_badge light g_mayuscula" style="font-size:11px;">{{ $bloque->responsable_rol }}</span>
                </div>

                {{-- ACCIONES DE ESTADO --}}
                <div class="cabecera_titulo_botones">
                    @if($bloque->estado === 'COMPLETADO')
                        <span class="g_badge success"><i class="fa-solid fa-check"></i> Completado</span>
                    @else
                        @if($bloque->estado !== 'EN_CURSO')
                            <button wire:click="actualizarEstado({{ $bloque->id }}, 'EN_CURSO')" class="g_boton warning">
                                <i class="fa-solid fa-play"></i> Iniciar
                            </button>
                        @else
                            <span class="g_badge warning"><i class="fa-solid fa-spinner fa-spin"></i> En Curso</span>
                        @endif
                        <button wire:click="actualizarEstado({{ $bloque->id }}, 'COMPLETADO')" class="g_boton success">
                            <i class="fa-solid fa-check"></i> Completar
                        </button>
                    @endif
                </div>
            </div>

            {{-- CHECKLIST --}}
            @if($bloque->checklists->count() > 0)
                <div style="margin-top:12px; padding-top:12px; border-top: 1px solid var(--borde-card-color, #e5e7eb);">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 8px 0; font-size:11px;">Checklist</p>
                    <div class="g_gap_pagina" style="gap:6px;">
                        @foreach($bloque->checklists as $item)
                            <div style="display:flex; align-items:center; gap:10px;">
                                <button wire:click="toggleChecklist({{ $item->id }})"
                                    class="g_boton {{ $item->esta_listo ? 'success' : 'light' }}"
                                    style="width:28px; height:28px; padding:0; min-width:28px; border-radius:50%;">
                                    <i class="fa-solid {{ $item->esta_listo ? 'fa-check' : 'fa-circle' }}"
                                        style="font-size:11px;"></i>
                                </button>
                                <span style="{{ $item->esta_listo ? 'text-decoration:line-through; opacity:0.5;' : '' }}">
                                    {{ $item->tarea }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="g_panel">
            <div class="g_alerta info">
                <i class="fa-solid fa-circle-info"></i>
                No hay bloques de itinerario configurados aún para este evento.
            </div>
        </div>
    @endforelse

</div>