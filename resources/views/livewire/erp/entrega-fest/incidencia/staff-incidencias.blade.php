<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Canal de Incidencias
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            @can('entrega-fest.staff')
                <a href="{{ route('erp.entrega-fest.incidencia.crear', $evento->id) }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    {{-- LISTA DE INCIDENCIAS --}}
    <div class="g_gap_pagina" style="display: flex; flex-direction: column; gap: 15px;">
        @forelse($incidencias as $inc)
            <div class="g_panel"
                style="border-left: 4px solid {{ $inc->prioridad === 'ALTA' ? 'var(--color-danger)' : ($inc->prioridad === 'MEDIA' ? 'var(--color-warning)' : 'var(--color-info)') }}; padding: 0; overflow: hidden;">
                
                {{-- CARD HEADER: Estados y Acciones --}}
                <div style="background: rgba(0,0,0,0.02); padding: 10px 20px; border-bottom: 1px solid var(--borde-card-color, #eee); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="g_inferior g_mayuscula" style="font-weight: 800; opacity: 0.6;">#{{ $inc->id }}</span>
                        <span class="g_badge light" style="font-size: 10px;">{{ $inc->tipo }}</span>
                        
                        {{-- Prioridad Badge (Read-only) --}}
                        <span class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}" style="font-size: 10px;">
                            {{ $inc->prioridad }}
                        </span>
                    </div>

                    <div style="display: flex; gap: 10px; align-items: center;">
                        {{-- Botones de Estado tipo Itinerario --}}
                        <div class="cabecera_titulo_botones">
                            @can('entrega-fest.staff')
                                <a href="{{ route('erp.entrega-fest.incidencia.editar', [$evento->id, $inc->id]) }}"
                                    class="g_accion editar" title="Editar incidencia">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>

                                @if($inc->estado === 'RESUELTO')
                                    <span class="g_badge success"><i class="fa-solid fa-check-double"></i> Resuelto</span>
                                    <button wire:click="cambiarEstado({{ $inc->id }}, 'ABIERTO')" class="g_boton dark small" style="padding: 4px 8px; font-size: 10px;">
                                        Reabrir
                                    </button>
                                @elseif($inc->estado === 'PROCESO')
                                    <span class="g_badge warning"><i class="fa-solid fa-spinner fa-spin"></i> En Curso</span>
                                    <button wire:click="cambiarEstado({{ $inc->id }}, 'RESUELTO')" class="g_boton success">
                                        <i class="fa-solid fa-check"></i> Finalizar
                                    </button>
                                @else
                                    <button wire:click="cambiarEstado({{ $inc->id }}, 'PROCESO')" class="g_boton warning">
                                        <i class="fa-solid fa-play"></i> En Curso
                                    </button>
                                @endif
                            @else
                                <span class="g_badge {{ $inc->estado === 'RESUELTO' ? 'success' : ($inc->estado === 'PROCESO' ? 'warning' : 'light') }}" style="font-size: 11px;">
                                    {{ $inc->estado }}
                                </span>
                            @endcan
                        </div>
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="g_fila" style="gap: 0;">
                    <div class="g_columna_7" style="padding: 20px;">
                        <p style="font-size: 15px; font-weight: 600; color: var(--color-dark); margin: 0 0 12px 0; line-height: 1.5;">
                            {{ $inc->descripcion }}
                        </p>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 15px; color: var(--color-gray); font-size: 12px;">
                            @if($inc->ubicacion)
                                <span><i class="fa-solid fa-location-dot" style="color: var(--color-danger);"></i> {{ $inc->ubicacion }}</span>
                            @endif
                            <span><i class="fa-solid fa-user"></i> {{ $inc->informante->name }}</span>
                            <span><i class="fa-solid fa-clock"></i> {{ $inc->created_at->diffForHumans() }}</span>
                        </div>

                        @if($inc->media->count() > 0)
                            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:15px; padding-top:12px; border-top: 1px dotted var(--borde-card-color, #eee);">
                                @foreach($inc->getMedia('evidencias') as $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" style="display: block;">
                                        <img src="{{ $media->getUrl() }}"
                                            style="width:65px; height:65px; object-fit:cover; border-radius:8px; border: 1px solid var(--borde-card-color, #eee);">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Gestión --}}
                    <div class="g_columna_5" style="background: rgba(0,0,0,0.01); border-left: 1px solid var(--borde-card-color, #eee); padding: 20px;">
                        <p class="g_inferior g_mayuscula" style="margin: 0 0 12px 0; font-size: 10px; font-weight: 700; color: var(--color-gray);">
                            <i class="fa-solid fa-toolbox"></i> Gestión de Resolución
                        </p>

                        @can('entrega-fest.staff')
                            <div style="margin-bottom: 15px;">
                                <label class="g_inferior" style="display: block; margin-bottom: 4px;">Responsable:</label>
                                <select wire:change="asignarResponsable({{ $inc->id }}, $event.target.value)" class="g_input"
                                    style="font-size: 12px; height: 34px;">
                                    <option value="">-- Sin asignar --</option>
                                    @foreach($staff_users as $u)
                                        <option value="{{ $u->id }}" {{ $inc->responsable_user_id == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="g_inferior" style="display: block; margin-bottom: 4px;">Bitácora de Solución:</label>
                                <textarea 
                                    wire:blur="guardarSolucion({{ $inc->id }}, $event.target.value)"
                                    class="g_input" 
                                    rows="3" 
                                    placeholder="Detalles sobre la solución..." 
                                    style="font-size: 12px; min-height: 80px;">{{ $inc->solucion }}</textarea>
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                @if($inc->responsable)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <i class="fa-solid fa-user-gear" style="color: var(--color-info); font-size: 14px;"></i>
                                        <span style="font-size: 13px; color: var(--color-dark);">{{ $inc->responsable->name }}</span>
                                    </div>
                                @endif

                                @if($inc->solucion)
                                    <div style="background: var(--bg-success, #ecfdf5); border: 1px solid var(--color-success); padding: 10px; border-radius: 8px;">
                                        <p style="font-size: 11px; font-weight: 800; color: var(--color-success); margin: 0 0 4px 0;">
                                            SOLUCIÓN
                                        </p>
                                        <p style="font-size: 12px; color: var(--color-dark); margin: 0; line-height: 1.4;">{{ $inc->solucion }}</p>
                                    </div>
                                @else
                                    <div class="g_alerta info" style="padding: 8px; font-size: 11px; margin: 0;">
                                        <i class="fa-solid fa-circle-info"></i> Resolución pendiente
                                    </div>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="g_panel" style="text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-shield-check" style="font-size: 3rem; color: var(--color-success); opacity: 0.2; margin-bottom: 15px;"></i>
                <h3 class="g_panel_titulo">¡No hay incidencias!</h3>
                <p class="g_inferior">Todo parece estar en orden en este evento.</p>
            </div>
        @endforelse
    </div>
</div>