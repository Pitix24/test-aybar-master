<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Protocolos y Discursos
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            @can('entrega-fest.staff')
                <a href="{{ route('erp.entrega-fest.protocolo.crear', $evento->id) }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_gap_pagina" style="gap:15px;">
        @forelse($evento->protocolos as $protocolo)
            <div class="g_panel" style="border-left:4px solid var(--color-vivo); position:relative;">

                {{-- Acciones (Solo Staff/Admin) --}}
                @can('entrega-fest.staff')
                    <div style="position: absolute; top: 10px; right: 10px; display:flex; gap:5px; z-index:10;">
                        <a href="{{ route('erp.entrega-fest.protocolo.editar', [$evento->id, $protocolo->id]) }}"
                            class="g_boton primary small"
                            style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-pencil" style="font-size:10px;"></i>
                        </a>
                        <button type="button"
                            onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarProtocoloOn', titulo: '¿Eliminar Protocolo?', texto: 'Esta acción no se puede deshacer.', id: {{ $protocolo->id }} })"
                            class="g_boton danger small"
                            style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-trash" style="font-size:10px;"></i>
                        </button>
                    </div>
                @endcan

                <h4 class="g_panel_titulo" style="padding-right: 60px;">{{ $protocolo->titulo }}</h4>
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