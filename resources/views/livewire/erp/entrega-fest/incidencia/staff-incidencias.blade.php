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
    <div class="g_panel_dashboard_grid">
        @forelse($incidencias as $inc)
            <div class="g_panel"
                style="border-left: 4px solid {{ $inc->prioridad === 'ALTA' ? 'var(--color-danger)' : ($inc->prioridad === 'MEDIA' ? 'var(--color-warning)' : 'var(--color-info)') }}; position: relative;">

                {{-- Acciones (Solo Staff/Admin) --}}
                @can('entrega-fest.staff')
                    <div style="position: absolute; top: 10px; right: 10px; display:flex; gap:5px;">
                        <a href="{{ route('erp.entrega-fest.incidencia.editar', [$evento->id, $inc->id]) }}"
                            class="g_boton primary small"
                            style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-pencil" style="font-size:10px;"></i>
                        </a>
                        <button type="button"
                            onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarIncidenciaOn', titulo: '¿Eliminar Incidencia?', texto: 'Esta acción no se puede deshacer.', id: {{ $inc->id }} })"
                            class="g_boton danger small"
                            style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-trash" style="font-size:10px;"></i>
                        </button>
                    </div>
                @endcan

                <div
                    style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:10px; padding-right: 60px;">
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <span class="g_badge light g_mayuscula" style="font-size:11px;">{{ $inc->tipo }}</span>

                        {{-- Cambio de Prioridad (Solo Staff/Admin) --}}
                        @can('entrega-fest.staff')
                            <select wire:change="cambiarPrioridad({{ $inc->id }}, $event.target.value)"
                                class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}"
                                style="border:none; cursor:pointer; font-size:11px;">
                                <option value="BAJA" {{ $inc->prioridad === 'BAJA' ? 'selected' : '' }}>BAJA</option>
                                <option value="MEDIA" {{ $inc->prioridad === 'MEDIA' ? 'selected' : '' }}>MEDIA</option>
                                <option value="ALTA" {{ $inc->prioridad === 'ALTA' ? 'selected' : '' }}>ALTA</option>
                            </select>
                        @else
                            <span
                                class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}"
                                style="font-size:11px;">
                                {{ $inc->prioridad }}
                            </span>
                        @endcan
                    </div>

                    {{-- Cambio de Estado (Solo Staff/Admin) --}}
                    @can('entrega-fest.staff')
                        <select wire:change="cambiarEstado({{ $inc->id }}, $event.target.value)" class="g_badge light"
                            style="border:none; cursor:pointer; font-size:11px;">
                            <option value="ABIERTO" {{ $inc->estado === 'ABIERTO' ? 'selected' : '' }}>ABIERTO</option>
                            <option value="PROCESO" {{ $inc->estado === 'PROCESO' ? 'selected' : '' }}>PROCESO</option>
                            <option value="RESUELTO" {{ $inc->estado === 'RESUELTO' ? 'selected' : '' }}>RESUELTO</option>
                        </select>
                    @else
                        <span class="g_badge light g_mayuscula" style="font-size:11px;">{{ $inc->estado }}</span>
                    @endcan
                </div>

                <p class="g_negrita" style="margin:0 0 4px 0;">{{ $inc->descripcion }}</p>

                <div class="g_fila" style="margin-top:10px; gap:15px;">
                    <div class="g_columna_6">
                        @if($inc->ubicacion)
                            <p class="g_inferior" style="margin:0 0 4px 0;">
                                <i class="fa-solid fa-location-dot"></i> {{ $inc->ubicacion }}
                            </p>
                        @endif
                        <p class="g_inferior" style="margin:0;">
                            <i class="fa-solid fa-user"></i> {{ $inc->informante->name }}
                            &bull;
                            <i class="fa-solid fa-clock"></i> {{ $inc->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="g_columna_6">
                        @can('entrega-fest.staff')
                            <label class="g_inferior" style="display:block; margin-bottom:2px;">Responsable:</label>
                            <select wire:change="asignarResponsable({{ $inc->id }}, $event.target.value)" class="g_input"
                                style="font-size:11px; padding:4px 8px;">
                                <option value="">Sin asignar</option>
                                @foreach($staff_users as $u)
                                    <option value="{{ $u->id }}" {{ $inc->responsable_user_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            @if($inc->responsable)
                                <p class="g_inferior" style="color:var(--color-vivo);">
                                    <i class="fa-solid fa-user-gear"></i> Responsable: {{ $inc->responsable->name }}
                                </p>
                            @endif
                        @endcan
                    </div>
                </div>

                @if($inc->media->count() > 0)
                    <div
                        style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; padding-top:12px; border-top:1px solid #eee;">
                        @foreach($inc->getMedia('evidencias') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank">
                                <img src="{{ $media->getUrl() }}"
                                    style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid var(--borde-card-color, #e5e7eb);">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="g_alerta success" style="grid-column: 1/-1;">
                <i class="fa-solid fa-circle-check"></i>
                No hay incidencias reportadas. ¡Todo bajo control!
            </div>
        @endforelse
    </div>

</div>