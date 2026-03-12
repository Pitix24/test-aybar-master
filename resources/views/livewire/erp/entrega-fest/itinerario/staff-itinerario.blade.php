<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Itinerario en Vivo
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.ver-staff')
                <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                    <i class="fa-solid fa-grip"></i> Panel de Staff
                </a>
            @endcan

            @can('itinerario.crear')
                <a href="{{ route('erp.entrega-fest.itinerario.crear', $evento->id) }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    @forelse($evento->itinerarioBloques as $bloque)
        <div class="g_panel"
            style="border-left: 4px solid {{ $bloque->estado === 'COMPLETADO' ? 'var(--color-success)' : ($bloque->estado === 'CURSO' ? 'var(--color-warning)' : 'var(--borde-card-color, #e5e7eb)') }};">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
                <div style="flex:1;">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 2px 0; font-size:11px;">
                        <i class="fa-solid fa-clock"></i>
                        {{ $bloque->hora_inicio }} {{ $bloque->hora_fin ? '— ' . $bloque->hora_fin : '' }}
                        @if($bloque->ubicacion)
                            &nbsp;&bull;&nbsp; <i class="fa-solid fa-location-dot"></i> {{ $bloque->ubicacion }}
                        @endif
                    </p>
                    <h4 class="g_panel_titulo" style="margin:2px 0 4px 0;">{{ $bloque->titulo }}</h4>
                </div>

                <div class="cabecera_titulo_botones">
                    @can('itinerario.editar')
                        <a href="{{ route('erp.entrega-fest.itinerario.editar', [$evento->id, $bloque->id]) }}"
                            class="g_accion editar" title="Editar bloque">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                    @endcan

                    @if($bloque->estado === 'COMPLETADO')
                        <span class="g_badge success"><i class="fa-solid fa-check"></i> Completado</span>
                    @elseif($bloque->estado === 'CURSO')
                        <span class="g_badge warning"><i class="fa-solid fa-spinner fa-spin"></i> En Curso</span>
                        @can('itinerario.marcar-tarea')
                            <button wire:click="actualizarEstado({{ $bloque->id }}, 'COMPLETADO')" class="g_boton success">
                                <i class="fa-solid fa-check"></i> Finalizar
                            </button>
                        @endcan
                    @else
                        @can('itinerario.marcar-tarea')
                            <button wire:click="actualizarEstado({{ $bloque->id }}, 'CURSO')" class="g_boton warning">
                                <i class="fa-solid fa-play"></i> Iniciar
                            </button>
                        @endcan
                    @endif
                </div>
            </div>

            @if($bloque->checklists->count() > 0)
                <div style="margin-top:12px; padding-top:12px; border-top: 1px solid var(--borde-card-color, #e5e7eb);">
                    <p class="g_inferior g_mayuscula" style="margin:0 0 8px 0; font-size:10px;">
                        <i class="fa-solid fa-list-check"></i> Checklist
                    </p>
                    <div class="g_gap_pagina" style="gap:6px;">
                        @foreach($bloque->checklists as $item)
                            <div style="display:flex; align-items:center; gap:10px;">
                                @can('itinerario.marcar-tarea')
                                    <button wire:click="toggleChecklist({{ $item->id }})"
                                        class="g_boton {{ $item->esta_listo ? 'success' : 'light' }}"
                                        style="width:28px; height:28px; padding:0; min-width:28px; border-radius:50%;">
                                        <i class="fa-solid {{ $item->esta_listo ? 'fa-check' : 'fa-circle' }}"
                                            style="font-size:11px;"></i>
                                    </button>
                                @endcan
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