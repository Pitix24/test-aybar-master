<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            <span>{{ $evento->nombre }}</span>
            Recursos y Apoyo
        </h2>
        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.entrega-fest.vista.staff.dashboard', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Panel Staff
            </a>
        </div>
    </div>

    {{-- TABS --}}
    <div class="g_panel">
        <div class="g_tab_navegacion">
            <div class="g_tab_botones">
                <button wire:click="$set('tab', 'MAPAS')"
                    class="g_tab_boton {{ $tab === 'MAPAS' ? 'g_tab_active' : 'g_tab_inactive' }}">
                    <i class="fa-solid fa-file-lines"></i> Mapas y Manuales
                </button>
                <button wire:click="$set('tab', 'PROTOCOLOS')"
                    class="g_tab_boton {{ $tab === 'PROTOCOLOS' ? 'g_tab_active' : 'g_tab_inactive' }}">
                    <i class="fa-solid fa-scroll"></i> Protocolos / Discursos
                </button>
                <button wire:click="$set('tab', 'CONTINGENCIAS')"
                    class="g_tab_boton {{ $tab === 'CONTINGENCIAS' ? 'g_tab_active' : 'g_tab_inactive' }}">
                    <i class="fa-solid fa-shield-halved"></i> Plan de Contingencia
                </button>
            </div>
        </div>

        <div class="g_tab_content g_gap_pagina" style="margin-top:15px;">

            @if($tab === 'MAPAS')
                <div class="g_panel_dashboard_grid">
                    @forelse($evento->recursos as $recurso)
                        <div class="g_panel" style="padding:0; overflow:hidden;">
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
                                    {{ $recurso->tipo_recurso }}</p>
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

            @elseif($tab === 'PROTOCOLOS')
                <div class="g_gap_pagina">
                    @forelse($evento->protocolos as $protocolo)
                        <div class="g_panel" style="border-left:4px solid var(--color-vivo);">
                            <h4 class="g_panel_titulo">{{ $protocolo->titulo }}</h4>
                            <p style="line-height:1.7; font-style:italic; color:var(--color-light-texto); margin:0;">
                                "{{ $protocolo->contenido }}"
                            </p>
                        </div>
                    @empty
                        <div class="g_alerta info">
                            <i class="fa-solid fa-circle-info"></i> No hay protocolos cargados aún.
                        </div>
                    @endforelse
                </div>

            @elseif($tab === 'CONTINGENCIAS')
                <div class="g_gap_pagina">
                    @forelse($evento->contingencias as $plan)
                        <div class="g_panel" style="border-left:4px solid var(--color-danger);">
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
            @endif

        </div>
    </div>

</div>