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
    <div class="g_gap_pagina" style="display: flex; flex-direction: column; gap: 20px;">
        @forelse($incidencias as $inc)
            <div class="g_panel"
                style="border-left: 5px solid {{ $inc->prioridad === 'ALTA' ? '#ef4444' : ($inc->prioridad === 'MEDIA' ? '#f59e0b' : '#3b82f6') }}; padding: 0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                
                {{-- CARD HEADER: Estados y Acciones --}}
                <div style="background: #fafafa; padding: 12px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <span style="font-size: 11px; font-weight: 800; color: #888; letter-spacing: 0.5px;">INC-#{{ $inc->id }}</span>
                        <span class="g_badge light" style="font-size: 11px; border: 1px solid #ddd;">{{ $inc->tipo }}</span>
                        
                        {{-- Prioridad Selector / Badge --}}
                        @can('entrega-fest.staff')
                            <select wire:change="cambiarPrioridad({{ $inc->id }}, $event.target.value)"
                                class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}"
                                style="border:none; cursor:pointer; font-size: 11px; height: 22px; padding: 0 8px;">
                                <option value="BAJA" {{ $inc->prioridad === 'BAJA' ? 'selected' : '' }}>BAJA</option>
                                <option value="MEDIA" {{ $inc->prioridad === 'MEDIA' ? 'selected' : '' }}>MEDIA</option>
                                <option value="ALTA" {{ $inc->prioridad === 'ALTA' ? 'selected' : '' }}>ALTA</option>
                            </select>
                        @else
                            <span class="g_badge {{ $inc->prioridad === 'ALTA' ? 'danger' : ($inc->prioridad === 'MEDIA' ? 'warning' : 'info') }}" style="font-size: 11px;">
                                {{ $inc->prioridad }}
                            </span>
                        @endcan
                    </div>

                    <div style="display: flex; gap: 15px; align-items: center;">
                        {{-- Estado Selector --}}
                        @can('entrega-fest.staff')
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <label style="font-size: 11px; color: #777; font-weight: 600;">ESTADO:</label>
                                <select wire:change="cambiarEstado({{ $inc->id }}, $event.target.value)" 
                                    class="g_badge {{ $inc->estado === 'RESUELTO' ? 'success' : 'light' }}"
                                    style="border:1px solid #ddd; cursor:pointer; font-size: 11px; height: 26px; padding: 0 10px;">
                                    <option value="ABIERTO" {{ $inc->estado === 'ABIERTO' ? 'selected' : '' }}>ABIERTO</option>
                                    <option value="PROCESO" {{ $inc->estado === 'PROCESO' ? 'selected' : '' }}>PROCESO</option>
                                    <option value="RESUELTO" {{ $inc->estado === 'RESUELTO' ? 'selected' : '' }}>RESUELTO</option>
                                </select>
                            </div>

                            <div style="display: flex; gap: 6px; border-left: 1px solid #ddd; padding-left: 15px; margin-left: 5px;">
                                <a href="{{ route('erp.entrega-fest.incidencia.editar', [$evento->id, $inc->id]) }}"
                                    class="g_boton info small" title="Editar"
                                    style="width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius: 6px;">
                                    <i class="fa-solid fa-pencil" style="font-size:11px;"></i>
                                </a>
                                <button type="button"
                                    onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarIncidenciaOn', titulo: '¿Eliminar Incidencia?', texto: 'Esta acción no se puede deshacer.', id: {{ $inc->id }} })"
                                    class="g_boton danger small" title="Eliminar"
                                    style="width:28px; height:28px; padding:0; display:flex; align-items:center; justify-content:center; border-radius: 6px;">
                                    <i class="fa-solid fa-trash" style="font-size:11px;"></i>
                                </button>
                            </div>
                        @else
                            <span class="g_badge {{ $inc->estado === 'RESUELTO' ? 'success' : 'light' }}" style="font-size: 11px; font-weight: 700;">{{ $inc->estado }}</span>
                        @endcan
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="g_fila" style="gap: 0;">
                    {{-- Información del Reporte --}}
                    <div class="g_columna_7" style="padding: 25px;">
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0 0 15px 0; line-height: 1.4;">
                            {{ $inc->descripcion }}
                        </h3>
                        
                        <div style="display: flex; flex-wrap: wrap; gap: 20px; color: #6b7280; font-size: 13px;">
                            @if($inc->ubicacion)
                                <span><i class="fa-solid fa-location-dot" style="color: #ef4444; margin-right: 5px;"></i> <strong>Ubicación:</strong> {{ $inc->ubicacion }}</span>
                            @endif
                            <span><i class="fa-solid fa-user" style="margin-right: 5px;"></i> <strong>Reportó:</strong> {{ $inc->informante->name }}</span>
                            <span><i class="fa-solid fa-clock" style="margin-right: 5px;"></i> {{ $inc->created_at->diffForHumans() }}</span>
                        </div>

                        @if($inc->media->count() > 0)
                            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:20px; padding-top:15px; border-top: 1px dashed #eee;">
                                @foreach($inc->getMedia('evidencias') as $media)
                                    <a href="{{ $media->getUrl() }}" target="_blank" style="transition: transform 0.2s; display: block;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        <img src="{{ $media->getUrl() }}"
                                            style="width:70px; height:70px; object-fit:cover; border-radius:10px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Gestión de la Resolución --}}
                    <div class="g_columna_5" style="background: #fbfbfb; border-left: 1px solid #eee; padding: 25px;">
                        <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: #4b5563; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-toolbox" style="color: #6366f1;"></i> Gestión de Resolución
                        </h4>

                        @can('entrega-fest.staff')
                            <div style="margin-bottom: 15px;">
                                <label style="font-size: 11px; font-weight: 700; color: #6b7280; display: block; margin-bottom: 6px;">Persona Responsable:</label>
                                <select wire:change="asignarResponsable({{ $inc->id }}, $event.target.value)" class="g_input"
                                    style="font-size: 13px; height: 38px; background: #fff;">
                                    <option value="">-- Seleccionar Responsable --</option>
                                    @foreach($staff_users as $u)
                                        <option value="{{ $u->id }}" {{ $inc->responsable_user_id == $u->id ? 'selected' : '' }}>
                                            {{ $u->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label style="font-size: 11px; font-weight: 700; color: #6b7280; display: block; margin-bottom: 6px;">Solución / Bitácora:</label>
                                <textarea 
                                    wire:blur="guardarSolucion({{ $inc->id }}, $event.target.value)"
                                    class="g_input" 
                                    rows="3" 
                                    placeholder="Describe cómo se resolvió la incidencia..." 
                                    style="font-size: 13px; background: #fff; min-height: 100px; padding: 12px; line-height: 1.5;">{{ $inc->solucion }}</textarea>
                                <p style="font-size: 10px; color: #9ca3af; margin-top: 6px; font-style: italic;">
                                    <i class="fa-solid fa-info-circle"></i> Los cambios se guardan al salir del campo.
                                </p>
                            </div>
                        @else
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                @if($inc->responsable)
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 32px; height: 32px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4338ca;">
                                            <i class="fa-solid fa-user-gear" style="font-size: 14px;"></i>
                                        </div>
                                        <div>
                                            <p style="font-size: 11px; font-weight: 700; color: #6b7280; margin: 0;">RESPONSABLE</p>
                                            <p style="font-size: 13px; font-weight: 600; color: #1f2937; margin: 0;">{{ $inc->responsable->name }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($inc->solucion)
                                    <div style="background: #ecfdf5; border: 1px solid #d1fae5; padding: 15px; border-radius: 12px;">
                                        <p style="font-size: 11px; font-weight: 800; color: #065f46; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                                            <i class="fa-solid fa-check-circle" style="margin-right: 5px;"></i> Resolución oficial
                                        </p>
                                        <p style="font-size: 13px; color: #064e3b; margin: 0; line-height: 1.5; white-space: pre-wrap;">{{ $inc->solucion }}</p>
                                    </div>
                                @else
                                    <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 12px; border-radius: 10px; display: flex; align-items: center; gap: 10px;">
                                        <i class="fa-solid fa-triangle-exclamation" style="color: #d97706;"></i>
                                        <p style="font-size: 12px; color: #92400e; margin: 0; font-weight: 600;">Pendiente de resolución</p>
                                    </div>
                                @endif
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="g_panel" style="text-align: center; padding: 60px 20px; color: #6b7280;">
                <div style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 10px;">¡Todo bajo control!</h3>
                <p>No hay incidencias reportadas en este momento para el evento.</p>
            </div>
        @endforelse
    </div>

</div>