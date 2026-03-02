<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Canal de Incidencias
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <button wire:click="$toggle('mostrarFormulario')"
                class="g_boton {{ $mostrarFormulario ? 'cancelar' : 'danger' }}">
                <i class="fa-solid {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
                {{ $mostrarFormulario ? 'Cancelar' : 'Reportar Incidencia' }}
            </button>
            <a href="{{ route('erp.entrega-fest.vista.staff.dashboard', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Panel Staff
            </a>
        </div>
    </div>

    {{-- FORMULARIO DE REPORTE --}}
    @if($mostrarFormulario)
        <div class="g_panel g_gap_pagina">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-triangle-exclamation"></i> Nueva Incidencia</h4>

            <form wire:submit.prevent="reportar" class="formulario g_gap_pagina">
                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Tipo de Problema</label>
                        <select wire:model="tipo">
                            <option>Logística</option>
                            <option>Seguridad</option>
                            <option>Técnico</option>
                            <option>Salud</option>
                            <option>Otro</option>
                        </select>
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Prioridad</label>
                        <select wire:model="prioridad">
                            <option>Baja</option>
                            <option>Media</option>
                            <option>Alta</option>
                        </select>
                    </div>
                </div>

                <div class="g_margin_bottom_10">
                    <label>Descripción de los hechos <span class="obligatorio"><i
                                class="fa-solid fa-asterisk"></i></span></label>
                    <textarea wire:model="descripcion" rows="3" placeholder="¿Qué pasó? Sea específico..."></textarea>
                    @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>

                <div class="g_fila">
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Ubicación exacta</label>
                        <input type="text" wire:model="ubicacion" placeholder="Ej: Puerta 2, Detrás del escenario">
                    </div>
                    <div class="g_columna_6 g_margin_bottom_10">
                        <label>Evidencia fotográfica</label>
                        <input type="file" wire:model="fotos" multiple>
                        @error('fotos.*') <p class="mensaje_error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="formulario_botones">
                    <button type="submit" class="g_boton danger" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="reportar"><i class="fa-solid fa-paper-plane"></i> Enviar
                            Reporte</span>
                        <span wire:loading wire:target="reportar"><i class="fa-solid fa-spinner fa-spin"></i>
                            Enviando...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- LISTA DE INCIDENCIAS --}}
    <div class="g_panel_dashboard_grid">
        @forelse($incidencias as $inc)
            <div class="g_panel"
                style="border-left: 4px solid {{ $inc->prioridad === 'Alta' ? 'var(--color-danger)' : ($inc->prioridad === 'Media' ? 'var(--color-warning)' : 'var(--color-info)') }};">

                <div
                    style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:8px; margin-bottom:10px;">
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <span class="g_badge light g_mayuscula" style="font-size:11px;">{{ $inc->tipo }}</span>

                        {{-- Cambio de Prioridad (Solo Staff/Admin) --}}
                        @can('entrega-fest.staff')
                            <select wire:change="cambiarPrioridad({{ $inc->id }}, $event.target.value)"
                                class="g_badge {{ $inc->prioridad === 'Alta' ? 'danger' : ($inc->prioridad === 'Media' ? 'warning' : 'info') }}"
                                style="border:none; cursor:pointer; font-size:11px;">
                                <option value="Baja" {{ $inc->prioridad === 'Baja' ? 'selected' : '' }}>Prioridad Baja</option>
                                <option value="Media" {{ $inc->prioridad === 'Media' ? 'selected' : '' }}>Prioridad Media</option>
                                <option value="Alta" {{ $inc->prioridad === 'Alta' ? 'selected' : '' }}>Prioridad Alta</option>
                            </select>
                        @else
                            <span
                                class="g_badge {{ $inc->prioridad === 'Alta' ? 'danger' : ($inc->prioridad === 'Media' ? 'warning' : 'info') }}"
                                style="font-size:11px;">
                                Prioridad {{ $inc->prioridad }}
                            </span>
                        @endcan
                    </div>

                    {{-- Cambio de Estado (Solo Staff/Admin) --}}
                    @can('entrega-fest.staff')
                        <select wire:change="cambiarEstado({{ $inc->id }}, $event.target.value)" class="g_badge light"
                            style="border:none; cursor:pointer; font-size:11px;">
                            <option value="Abierta" {{ $inc->estado === 'Abierta' ? 'selected' : '' }}>Abierta</option>
                            <option value="En Proceso" {{ $inc->estado === 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="Resuelta" {{ $inc->estado === 'Resuelta' ? 'selected' : '' }}>Resuelta</option>
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
                            <label class="g_inferior" style="display:block; margin-bottom:2px;">Asignar Responsable:</label>
                            <select wire:change="asignarResponsable({{ $inc->id }}, $event.target.value)" class="g_input"
                                style="font-size:11px; padding:4px 8px;">
                                <option value="">Sin asignar</option>
                                @foreach($staff_users as $u)
                                    <option value="{{ $u->id }}" {{ $inc->responsable_user_id == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}</option>
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