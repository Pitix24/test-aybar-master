<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Recursos y Manuales
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.staff')
                <button wire:click="$toggle('mostrarFormulario')"
                    class="g_boton {{ $mostrarFormulario ? 'cancelar' : 'guardar' }}">
                    <i class="fa-solid {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
                    {{ $mostrarFormulario ? 'Cancelar' : 'Cargar Recurso' }}
                </button>
            @endcan
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Panel Staff
            </a>
        </div>
    </div>

    {{-- FORMULARIOS --}}
    @if($mostrarFormulario)
        <div class="g_panel">
            <h4 class="g_panel_titulo">
                <i class="fa-solid fa-file-arrow-up"></i>
                Añadir Recurso
            </h4>

            <form wire:submit.prevent="agregarRecurso" class="formulario g_gap_pagina">
                <div class="g_fila">
                    <div class="g_columna_6">
                        <label>Nombre del Documento / Mapa</label>
                        <input type="text" wire:model="nombre_publico" placeholder="Ej: Plano de Aforos">
                    </div>
                    <div class="g_columna_6">
                        <label>Tipo</label>
                        <select wire:model="tipo_recurso">
                            <option value="MAPA">Mapa / Plano</option>
                            <option value="MANUAL">Manual / Guía</option>
                            <option value="FOTO">Fotografía / Referencia</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label>Archivo (PDF o Imagen)</label>
                    <input type="file" wire:model="archivo">
                    <div wire:loading wire:target="archivo" class="g_inferior">Subiendo archivo...</div>
                </div>
                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar">Guardar Recurso</button>
                </div>
            </form>
        </div>
    @endif

    <div class="g_panel">
        <h4 class="g_panel_titulo"><i class="fa-solid fa-file-lines"></i> Galería de Documentos</h4>
        
        <div class="g_panel_dashboard_grid" style="margin-top:15px;">
            @forelse($evento->recursos as $recurso)
                <div class="g_panel" style="padding:0; overflow:hidden; position:relative;">
                    @can('entrega-fest.staff')
                        <button wire:click="eliminarRecurso({{ $recurso->id }})" class="g_boton danger small"
                            style="position:absolute; top:5px; right:5px; z-index:10; width:26px; height:26px; padding:0;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    @endcan
                    <div
                        style="height:120px; background:var(--color-claro); display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden;">
                        @if($recurso->media->count() > 0)
                            <img src="{{ $recurso->getFirstMediaUrl() }}"
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
                            <a href="{{ $recurso->getFirstMediaUrl() }}" target="_blank" class="g_boton primary"
                                style="width:100%; justify-content:center;">
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