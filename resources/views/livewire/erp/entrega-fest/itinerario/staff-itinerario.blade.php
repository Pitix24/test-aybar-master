<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Itinerario en Vivo
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.itinerario.crear', $evento->id) }}" class="g_boton guardar">
                <i class="fa-solid fa-plus"></i> Nuevo Bloque
            </a>
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton light">
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
                <div style="flex:1;">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 2px 0; font-size:11px;">
                        <i class="fa-solid fa-clock"></i>
                        {{ $bloque->hora_inicio }} {{ $bloque->hora_fin ? '— ' . $bloque->hora_fin : '' }}
                        @if($bloque->ubicacion)
                            &nbsp;&bull;&nbsp; <i class="fa-solid fa-location-dot"></i> {{ $bloque->ubicacion }}
                        @endif
                    </p>
                    <h4 class="g_panel_titulo" style="margin:2px 0 4px 0;">{{ $bloque->titulo }}</h4>
                    @if($bloque->responsable_rol)
                        <span class="g_badge light g_mayuscula" style="font-size:11px;">{{ $bloque->responsable_rol }}</span>
                    @endif
                </div>

                {{-- ACCIONES --}}
                <div class="cabecera_titulo_botones">
                    {{-- Botón Editar --}}
                    <a href="{{ route('erp.entrega-fest.itinerario.editar', [$evento->id, $bloque->id]) }}"
                        class="g_accion editar" title="Editar bloque">
                        <i class="fa-solid fa-pencil"></i>
                    </a>

                    {{-- Estado --}}
                    @if($bloque->estado === 'COMPLETADO')
                        <span class="g_badge success"><i class="fa-solid fa-check"></i> Completado</span>
                    @elseif($bloque->estado === 'EN_CURSO')
                        <span class="g_badge warning"><i class="fa-solid fa-spinner fa-spin"></i> En Curso</span>
                        <button wire:click="actualizarEstado({{ $bloque->id }}, 'COMPLETADO')" class="g_boton success">
                            <i class="fa-solid fa-check"></i> Completar
                        </button>
                    @else
                        <button wire:click="actualizarEstado({{ $bloque->id }}, 'EN_CURSO')" class="g_boton warning">
                            <i class="fa-solid fa-play"></i> Iniciar
                        </button>
                    @endif
                </div>
            </div>

            {{-- CHECKLIST --}}
            @if($bloque->checklists->count() > 0)
                <div style="margin-top:12px; padding-top:12px; border-top: 1px solid var(--borde-card-color, #e5e7eb);">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 8px 0; font-size:10px;">
                        <i class="fa-solid fa-list-check"></i> Checklist
                    </p>
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
                No hay bloques de itinerario aun. Crea el primero con el boton superior.
            </div>
        </div>
    @endforelse

</div>