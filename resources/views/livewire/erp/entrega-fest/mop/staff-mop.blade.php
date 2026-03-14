<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Mi Manual de Operaciones
            <span>{{ $evento->nombre }}</span>
        </h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.mop.todo', $evento->id) }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            <a href="{{ route('erp.entrega-fest.mop.tareas', $evento->id) }}" class="g_boton secondary">
                Gestionar MOP <i class="fa-solid fa-list-check"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.mop-plantilla.todo') }}" class="g_boton success">
                Plantillas <i class="fa-solid fa-paste"></i>
            </a>

            <a href="{{ route('erp.entrega-fest.mop.tareas.crear', $evento->id) }}" class="g_boton primary">
                Crear Tarea <i class="fa-solid fa-square-plus"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

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
                        <div style="position: relative;">
                            @if($tarea->esta_completado)
                                <div class="g_badge success"
                                    style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; padding:0;">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            @else
                                <label for="evidence-{{ $tarea->id }}" class="g_boton light"
                                    style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; padding:0; cursor:pointer; border: 1px dashed var(--borde-card-color, #ccc);"
                                    wire:loading.class="disabled" wire:target="evidencias.{{ $tarea->id }}">
                                    <i class="fa-solid fa-camera" wire:loading.remove
                                        wire:target="evidencias.{{ $tarea->id }}"></i>
                                    <i class="fa-solid fa-spinner fa-spin" wire:loading
                                        wire:target="evidencias.{{ $tarea->id }}"></i>
                                </label>
                                <input type="file" id="evidence-{{ $tarea->id }}" wire:model="evidencias.{{ $tarea->id }}"
                                    style="display:none;" accept="image/*" capture="environment">
                            @endif
                        </div>

                        <div style="flex:1;">
                            <p>
                                {{ $tarea->titulo }}
                            </p>
                            @if($tarea->instruccion)
                                <p class="g_negrita"
                                    style="{{ $tarea->esta_completado ? 'text-decoration:line-through; opacity:0.5;' : '' }} margin:0 0 2px 0;">
                                    {{ $tarea->instruccion }}
                                </p>
                            @endif

                            @if($tarea->esta_completado && $tarea->getFirstMediaUrl('evidencias'))
                                <div style="margin-top:8px; display:flex; align-items:center; gap:10px;">
                                    <a href="{{ $tarea->getFirstMediaUrl('evidencias') }}" target="_blank">
                                        <img src="{{ $tarea->getFirstMediaUrl('evidencias') }}"
                                            style="width:60px; height:45px; object-fit:cover; border-radius:4px; border: 1px solid var(--borde-card-color, #eee);">
                                    </a>
                                    @if($tarea->completado_at)
                                        <p class="g_inferior" style="font-size:10px; opacity:0.7; margin:0;">
                                            <i class="fa-solid fa-clock"></i> {{ $tarea->completado_at->format('H:i') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
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

    <div class="g_resaltado_caja info">
        <span class="g_resaltado_caja_titulo"><i class="fa-solid fa-circle-info"></i> ¿Cómo funciona?</span>
        Este manual contiene tus responsabilidades específicas. Márcalas conforme las cumplas — los coordinadores verán
        tu progreso en tiempo real.
    </div>
</div>