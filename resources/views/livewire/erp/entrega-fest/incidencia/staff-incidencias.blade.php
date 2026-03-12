<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Canal de Incidencias
            <span>{{ $evento->nombre }}</span>
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
    <div class="g_gap_pagina" style="display: flex; flex-direction: column;">
        @forelse($incidencias as $inc)
            <div class="g_panel"
                style="border-left: 4px solid {{ $inc->prioridad === 'ALTA' ? 'var(--color-danger)' : ($inc->prioridad === 'MEDIA' ? 'var(--color-warning)' : 'var(--color-info)') }}; position: relative;">

                {{-- Acciones (Solo Staff/Admin) --}}
                @can('entrega-fest.staff')
                    <div style="position: absolute; top: 15px; right: 15px; display:flex; gap:8px;">
                        <a href="{{ route('erp.entrega-fest.incidencia.editar', [$evento->id, $inc->id]) }}"
                            class="g_boton primary small"
                            style="width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                            <i class="fa-solid fa-pencil" style="font-size:12px;"></i>
                        </a>
                        <button type="button"
                            onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarIncidenciaOn', titulo: '¿Eliminar Incidencia?', texto: 'Esta acción no se puede deshacer.', id: {{ $inc->id }} })"
                            class="g_boton danger small"
                            style="width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center; border-radius: 50%;">
                            <i class="fa-solid fa-trash" style="font-size:12px;"></i>
                        </button>
                    </div>
                @endcan

                <div
                    style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:15px; padding-right: 80px;">
                    <div style="display:flex; gap:8px; align-items: center;">
                        <span class="g_badge light g_mayuscula" style="font-size:12px; font-weight: 600;">{{ $inc->tipo }}</span>

                        {{-- Cambio de Prioridad (Solo Staff/Admin) --}}
                        @can('entrega-fest.staff')
                            <select wire:change="cambiarPrioridad({{ $inc->id }}, $event.target.value)"
                                class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}"
                                style="border:none; cursor:pointer; font-size:12px; font-weight: 600;">
                                <option value="BAJA" {{ $inc->prioridad === 'BAJA' ? 'selected' : '' }}>BAJA</option>
                                <option value="MEDIA" {{ $inc->prioridad === 'MEDIA' ? 'selected' : '' }}>MEDIA</option>
                                <option value="ALTA" {{ $inc->prioridad === 'ALTA' ? 'selected' : '' }}>ALTA</option>
                            </select>
                        @else
                            <span
                                class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}"
                                style="font-size:12px; font-weight: 600;">
                                {{ $inc->prioridad }}
                            </span>
                        @endcan
                    </div>

                    {{-- Cambio de Estado (Solo Staff/Admin) --}}
                    @can('entrega-fest.staff')
                        <select wire:change="cambiarEstado({{ $inc->id }}, $event.target.value)" class="g_badge light"
                            style="border:none; cursor:pointer; font-size:12px; font-weight: 600; background: #f0f0f0;">
                            <option value="ABIERTO" {{ $inc->estado === 'ABIERTO' ? 'selected' : '' }}>ABIERTO</option>
                            <option value="PROCESO" {{ $inc->estado === 'PROCESO' ? 'selected' : '' }}>PROCESO</option>
                            <option value="RESUELTO" {{ $inc->estado === 'RESUELTO' ? 'selected' : '' }}>RESUELTO</option>
                        </select>
                    @else
                        <span class="g_badge light g_mayuscula" style="font-size:12px; font-weight: 600;">{{ $inc->estado }}</span>
                    @endcan
                </div>

                <div class="g_fila" style="gap:20px;">
                    <div class="g_columna_7">
                        <p class="g_negrita" style="font-size:1.1rem; margin:0 0 10px 0; line-height: 1.4;">{{ $inc->descripcion }}</p>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                            @if($inc->ubicacion)
                                <p class="g_inferior" style="margin:0;">
                                    <i class="fa-solid fa-location-dot" style="color: var(--color-danger);"></i> <strong>Ubicación:</strong> {{ $inc->ubicacion }}
                                </p>
                            @endif
                            <p class="g_inferior" style="margin:0;">
                                <i class="fa-solid fa-user"></i> <strong>Reportado por:</strong> {{ $inc->informante->name }}
                            </p>
                            <p class="g_inferior" style="margin:0;">
                                <i class="fa-solid fa-clock"></i> {{ $inc->created_at->diffForHumans() }}
                            </p>
                        </div>

                        @if($inc->media->count() > 0)
                            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:15px; padding-top:15px; border-top:1px solid #f0f0f0;">
                                @foreach($inc->getMedia('evidencias') as $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" class="g_foto_preview">
                                        <img src="{{ $media->getUrl() }}"
                                            style="width:60px; height:60px; object-fit:cover; border-radius:8px; border:1px solid #ddd; transition: transform 0.2s;">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="g_columna_5" style="border-left: 1px solid #f0f0f0; padding-left: 20px;">
                        @can('entrega-fest.staff')
                            <div class="g_margin_bottom_10">
                                <label class="g_inferior" style="display:block; margin-bottom:5px; font-weight: 600;">Asignar Responsable:</label>
                                <select wire:change="asignarResponsable({{ $inc->id }}, $event.target.value)" class="g_input"
                                    style="font-size:13px; padding:6px 10px;">
                                    <option value="">Sin asignar</option>
                                    @foreach($staff_users as $u)
                                        <option value="{{ $u->id }}" {{ $inc->responsable_user_id == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="g_margin_bottom_10">
                                <label class="g_inferior" style="display:block; margin-bottom:5px; font-weight: 600;">Solución / Notas:</label>
                                <textarea 
                                    wire:blur="guardarSolucion({{ $inc->id }}, $event.target.value)"
                                    class="g_input" 
                                    rows="3" 
                                    placeholder="Escribe la solución aquí..." 
                                    style="font-size:13px; resize: vertical; min-height: 80px;">{{ $inc->solucion }}</textarea>
                                <p class="g_inferior" style="font-size: 10px; margin-top: 4px; opacity: 0.6;">* Se guarda automáticamente al salir del campo.</p>
                            </div>
                        @else
                            @if($inc->responsable)
                                <p class="g_inferior" style="color:var(--color-info); margin-bottom: 10px;">
                                    <i class="fa-solid fa-user-gear"></i> <strong>Responsable:</strong> {{ $inc->responsable->name }}
                                </p>
                            @endif

                            @if($inc->solucion)
                                <div style="background: #f9f9f9; padding: 12px; border-radius: 8px; border: 1px solid #eee;">
                                    <p class="g_inferior" style="font-weight: 600; margin-bottom: 5px; color: var(--color-success);">
                                        <i class="fa-solid fa-check-double"></i> Solución:
                                    </p>
                                    <p style="font-size: 13px; margin: 0; white-space: pre-wrap;">{{ $inc->solucion }}</p>
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="g_alerta success">
                <i class="fa-solid fa-circle-check"></i>
                No hay incidencias reportadas. ¡Todo bajo control!
            </div>
        @endforelse
    </div>

</div>