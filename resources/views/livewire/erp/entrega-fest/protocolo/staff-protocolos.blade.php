<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Protocolos y Discursos
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.staff')
                <button wire:click="$toggle('mostrarFormulario')"
                    class="g_boton {{ $mostrarFormulario ? 'cancelar' : 'guardar' }}">
                    <i class="fa-solid {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
                    {{ $mostrarFormulario ? 'Cancelar' : 'Nuevo Protocolo' }}
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
                <i class="fa-solid fa-feather"></i>
                Añadir Protocolo
            </h4>

            <form wire:submit.prevent="agregarProtocolo" class="formulario g_gap_pagina">
                <div>
                    <label>Título del Protocolo / Discurso</label>
                    <input type="text" wire:model="p_titulo" placeholder="Ej: Discurso de Bienvenida">
                </div>
                <div>
                    <label>Contenido / Texto Completo</label>
                    <textarea wire:model="p_contenido" rows="6"
                        placeholder="Escriba aquí el guión o pasos a seguir..."></textarea>
                </div>
                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar">Guardar Protocolo</button>
                </div>
            </form>
        </div>
    @endif

    <div class="g_gap_pagina">
        @forelse($evento->protocolos as $protocolo)
            <div class="g_panel" style="border-left:4px solid var(--color-vivo); position:relative;">
                @can('entrega-fest.staff')
                    <button wire:click="eliminarProtocolo({{ $protocolo->id }})" class="g_boton danger small"
                        style="position:absolute; top:10px; right:10px; width:26px; height:26px; padding:0;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                @endcan
                <h4 class="g_panel_titulo">{{ $protocolo->titulo }}</h4>
                <p
                    style="line-height:1.7; font-style:italic; color:var(--color-light-texto); margin:0; white-space: pre-wrap;">
                    "{{ $protocolo->contenido }}"
                </p>
            </div>
        @empty
            <div class="g_alerta info">
                <i class="fa-solid fa-circle-info"></i> No hay protocolos cargados aún.
            </div>
        @endforelse
    </div>

</div>