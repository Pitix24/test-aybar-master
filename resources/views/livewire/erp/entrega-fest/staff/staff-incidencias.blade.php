<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Canal de Incidencias
        </h2>
        <div class="cabecera_titulo_botones">
            <button wire:click="$toggle('mostrarFormulario')"
                class="g_boton {{ $mostrarFormulario ? 'cancelar' : 'danger' }}">
                <i class="fa-solid {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
                {{ $mostrarFormulario ? 'Cancelar' : 'Reportar Incidencia' }}
            </button>
            <a href="{{ route('erp.entrega-fest.staff.dashboard', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Panel Staff
            </a>
        </div>
    </div>

    {{-- FORMULARIO DE REPORTE --}}
    @if($mostrarFormulario)
        <div class="g_panel g_gap_pagina">
            <h4 class="g_panel_titulo"><i class="fa-solid fa-triangle-exclamation"></i> Nueva Incidencia</h4>

            <form wire:submit.prevent="reportar" class="g_gap_pagina">
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
                        <span
                            class="g_badge {{ $inc->prioridad === 'Alta' ? 'danger' : ($inc->prioridad === 'Media' ? 'warning' : 'info') }}"
                            style="font-size:11px;">
                            Prioridad {{ $inc->prioridad }}
                        </span>
                    </div>
                    <span class="g_badge light g_mayuscula" style="font-size:11px;">{{ $inc->estado }}</span>
                </div>

                <p class="g_negrita" style="margin:0 0 4px 0;">{{ $inc->descripcion }}</p>

                @if($inc->ubicacion)
                    <p class="g_inferior" style="margin:0 0 8px 0;">
                        <i class="fa-solid fa-location-dot"></i> {{ $inc->ubicacion }}
                    </p>
                @endif

                <p class="g_inferior" style="margin:0 0 8px 0;">
                    <i class="fa-solid fa-user"></i> {{ $inc->informante->name }}
                    &bull;
                    <i class="fa-solid fa-clock"></i> {{ $inc->created_at->diffForHumans() }}
                </p>

                @if($inc->media->count() > 0)
                    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;">
                        @foreach($inc->getMedia('evidencias') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank">
                                <img src="{{ $media->getUrl() }}"
                                    style="width:60px; height:60px; object-fit:cover; border-radius:6px; border:1px solid var(--borde-card-color, #e5e7eb);">
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