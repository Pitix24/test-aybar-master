<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Plan de Contingencia
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.staff')
                <button wire:click="$toggle('mostrarFormulario')"
                    class="g_boton {{ $mostrarFormulario ? 'cancelar' : 'guardar' }}">
                    <i class="fa-solid {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
                    {{ $mostrarFormulario ? 'Cancelar' : 'Nueva Contingencia' }}
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
                <i class="fa-solid fa-shield-virus"></i>
                Añadir Contingencia
            </h4>

            <form wire:submit.prevent="agregarContingencia" class="formulario g_gap_pagina">
                <div>
                    <label>Escenario de Riesgo</label>
                    <input type="text" wire:model="c_escenario" placeholder="Ej: Falla de energía eléctrica">
                </div>
                <div>
                    <label>Acción de Respuesta Inmediata</label>
                    <textarea wire:model="c_accion" rows="4" placeholder="¿Qué debe hacer el staff exactamente?"></textarea>
                </div>
                <div class="formulario_botones">
                    <button type="submit" class="g_boton guardar">Guardar Plan</button>
                </div>
            </form>
        </div>
    @endif

    <div class="g_gap_pagina">
        @forelse($evento->contingencias as $plan)
            <div class="g_panel" style="border-left:4px solid var(--color-danger); position:relative;">
                @can('entrega-fest.staff')
                    <button wire:click="eliminarContingencia({{ $plan->id }})" class="g_boton danger small"
                        style="position:absolute; top:10px; right:10px; width:26px; height:26px; padding:0;">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                @endcan
                <div class="g_resaltado_indicacion error" style="margin-bottom:8px;">
                    <i class="fa-solid fa-biohazard"></i>
                    <p class="g_negrita g_mayuscula" style="margin:0;">Escenario: {{ $plan->escenario }}</p>
                </div>
                <div class="g_resaltado_caja info">
                    <span class="g_resaltado_caja_titulo">Acción a tomar:</span>
                    {{ $plan->accion }}
                </div>
            </div>
        @empty
            <div class="g_alerta info">
                <i class="fa-solid fa-circle-info"></i> No hay planes de contingencia cargados aún.
            </div>
        @endforelse
    </div>

</div>