<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Plan de Contingencia
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff', $evento->id) }}" class="g_boton info">
                <i class="fa-solid fa-grip"></i> Panel de Staff
            </a>

            @can('entrega-fest.staff')
                <a href="{{ route('erp.entrega-fest.contingencia.crear', $evento->id) }}" class="g_boton primary">
                    Crear <i class="fa-solid fa-square-plus"></i>
                </a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <div class="g_gap_pagina" style="gap:15px;">
        @forelse($evento->contingencias as $plan)
            <div class="g_panel" style="border-left:4px solid var(--color-danger); position:relative;">

                {{-- Acciones (Solo Staff/Admin) --}}
                @can('entrega-fest.staff')
                    <div style="position: absolute; top: 10px; right: 10px; display:flex; gap:5px; z-index:10;">
                        <a href="{{ route('erp.entrega-fest.contingencia.editar', [$evento->id, $plan->id]) }}"
                            class="g_boton primary small"
                            style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-pencil" style="font-size:10px;"></i>
                        </a>
                        <button type="button"
                            onclick="Livewire.dispatch('alertaConfirmar', { event: 'eliminarContingenciaOn', titulo: '¿Eliminar Plan?', texto: 'Esta acción no se puede deshacer.', id: {{ $plan->id }} })"
                            class="g_boton danger small"
                            style="width:26px; height:26px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-trash" style="font-size:10px;"></i>
                        </button>
                    </div>
                @endcan

                <div class="g_resaltado_indicacion error" style="margin-bottom:8px; padding-right: 60px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <p class="g_negrita g_mayuscula" style="margin:0;">Escenario: {{ $plan->escenario }}</p>
                </div>
                <div class="g_resaltado_caja info">
                    <span class="g_resaltado_caja_titulo">Acción a tomar:</span>
                    <p style="white-space: pre-wrap; margin:0;">{{ $plan->accion }}</p>
                </div>
            </div>
        @empty
            <div class="g_alerta info">
                <i class="fa-solid fa-circle-info"></i> No hay planes de contingencia cargados aún.
            </div>
        @endforelse
    </div>

</div>