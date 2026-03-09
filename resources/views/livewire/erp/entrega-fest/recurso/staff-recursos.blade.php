<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Recursos y Manuales
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            @can('entrega-fest.staff')
                <a href="{{ route('erp.entrega-fest.recurso.crear', $evento->id) }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_panel">
        <h4 class="g_panel_titulo"><i class="fa-solid fa-file-lines"></i> Galería de Documentos</h4>

        <div class="g_panel_dashboard_grid" style="margin-top:15px;">
            @forelse($evento->recursos as $recurso)
                <div class="g_panel" style="padding:0; overflow:hidden; position:relative;">

                    {{-- Acciones (Solo Staff/Admin) --}}
                    @can('entrega-fest.staff')
                        <div style="position: absolute; top: 5px; right: 5px; display:flex; gap:5px; z-index:10;">
                            <a href="{{ route('erp.entrega-fest.recurso.editar', [$evento->id, $recurso->id]) }}"
                                class="g_boton primary small"
                                style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-pencil" style="font-size:10px;"></i>
                            </a>
                            <button type="button"
                                onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarRecursoOn', titulo: '¿Eliminar Recurso?', texto: 'Esta acción no se puede deshacer.', id: {{ $recurso->id }} })"
                                class="g_boton danger small"
                                style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-trash" style="font-size:10px;"></i>
                            </button>
                        </div>
                    @endcan

                    <div
                        style="height:120px; background:var(--color-claro); display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden;">
                        @if($recurso->media->count() > 0)
                            <img src="{{ $recurso->getFirstMediaUrl('recursos') ?: $recurso->getFirstMediaUrl() }}"
                                style="width:100%; height:100%; object-fit:cover;">
                        @else
                            <i class="fa-solid fa-file-pdf"
                                style="font-size:2.5rem; color:var(--color-danger); opacity:0.4;"></i>
                        @endif
                    </div>
                    <div style="padding:12px;">
                        <p class="g_inferior g_mayuscula" style="margin:0 0 4px 0; font-size:10px;">
                            {{ $recurso->tipo_recurso }}
                        </p>
                        <p class="g_negrita" style="margin:0 0 10px 0;">{{ $recurso->nombre_publico }}</p>
                        @if($recurso->media->count() > 0)
                            <a href="{{ $recurso->getFirstMediaUrl('recursos') ?: $recurso->getFirstMediaUrl() }}"
                                target="_blank" class="g_boton primary" style="width:100%; justify-content:center;">
                                <i class="fa-solid fa-eye"></i> Ver Documento
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="g_alerta info" style="grid-column:1/-1;">
                    <i class="fa-solid fa-circle-info"></i> No hay recursos cargados aún.
                </div>
            @endforelse
        </div>
    </div>

</div>