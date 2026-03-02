<div class="g_gap_pagina">

    {{-- CABECERA --}}
    <div class="g_panel cabecera_titulo_pagina">
        <h2>
            Recursos y Apoyo
            <span>{{ $evento->nombre }}</span>
        </h2>
        <div class="cabecera_titulo_botones">
            @can('entrega-fest.staff')
                <button wire:click="$toggle('mostrarFormulario')" class="g_boton {{ $mostrarFormulario ? 'cancelar' : 'guardar' }}">
                    <i class="fa-solid {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }}"></i>
                    {{ $mostrarFormulario ? 'Cancelar' : ($tab === 'MAPAS' ? 'Cargar Recurso' : ($tab === 'PROTOCOLOS' ? 'Nuevo Protocolo' : 'Nueva Contingencia')) }}
                </button>
            @endcan
            <a href="{{ route('erp.entrega-fest.vista.staff.dashboard', $evento->id) }}" class="g_boton light">
                <i class="fa-solid fa-arrow-left"></i> Panel Staff
            </a>
        </div>
    </div>

    {{-- FORMULARIOS DINÁMICOS --}}
    @if($mostrarFormulario)
        <div class="g_panel">
            <h4 class="g_panel_titulo">
                <i class="fa-solid {{ $tab === 'MAPAS' ? 'fa-file-arrow-up' : ($tab === 'PROTOCOLOS' ? 'fa-feather' : 'fa-shield-virus') }}"></i>
                Añadir {{ $tab === 'MAPAS' ? 'Recurso' : ($tab === 'PROTOCOLOS' ? 'Protocolo' : 'Contingencia') }}
            </h4>

            @if($tab === 'MAPAS')
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
            @elseif($tab === 'PROTOCOLOS')
                <form wire:submit.prevent="agregarProtocolo" class="formulario g_gap_pagina">
                    <div>
                        <label>Título del Protocolo / Discurso</label>
                        <input type="text" wire:model="p_titulo" placeholder="Ej: Discurso de Bienvenida">
                    </div>
                    <div>
                        <label>Contenido / Texto Completo</label>
                        <textarea wire:model="p_contenido" rows="6" placeholder="Escriba aquí el guión o pasos a seguir..."></textarea>
                    </div>
                    <div class="formulario_botones">
                        <button type="submit" class="g_boton guardar">Guardar Protocolo</button>
                    </div>
                </form>
            @elseif($tab === 'CONTINGENCIAS')
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
            @endif
        </div>
    @endif

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
                        <div class="g_panel" style="border-left:4px solid var(--color-vivo); position:relative;">
                            @can('entrega-fest.staff')
                                <button wire:click="eliminarProtocolo({{ $protocolo->id }})" class="g_boton danger small" 
                                    style="position:absolute; top:10px; right:10px; width:26px; height:26px; padding:0;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endcan
                            <h4 class="g_panel_titulo">{{ $protocolo->titulo }}</h4>
                            <p style="line-height:1.7; font-style:italic; color:var(--color-light-texto); margin:0; white-space: pre-wrap;">
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
            @endif

        </div>
    </div>

</div>