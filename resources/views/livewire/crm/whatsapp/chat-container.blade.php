{{-- =======================================================================
    CHAT CONTAINER — Orquestador principal del módulo WhatsApp
    Equivale a: pages/inicio-pagina + sidebars/whatsapp-izquierda + whatsapp-derecha
    ======================================================================= --}}
<div class="wsp_layout"
     x-data="{ }"
     @keydown.escape.window="$wire.cerrarTodo()">

    {{-- ══════════════════════════════════════════════════════════════════════
        PANEL IZQUIERDO
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_panel_izq" style="position:relative; overflow:hidden;">

        {{-- Contenido normal del panel izquierdo --}}
        <div style="display:flex; flex-direction:column; height:100%;">
            @livewire('crm.whatsapp.chat-lista')
        </div>

        {{-- ─ SIDEBAR: Perfil del agente ─────────────────────────────────── --}}
        <div class="wsp_sidebar_izq {{ $sidebarPerfil ? 'visible' : '' }}">
            <div class="wsp_sidebar_cabecera_verde">
                <i class="fa-solid fa-arrow-left wsp_sidebar_icon_back_blanco"
                   wire:click="cerrarPerfil" title="Cerrar"></i>
                <span>Perfil</span>
            </div>
            <div class="wsp_sidebar_cuerpo wsp_scroll">
                <div class="wsp_perfil_info">
                    <div class="wsp_perfil_avatar_grande">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="wsp_perfil_nombre">{{ auth()->user()->name ?? 'Agente' }}</div>
                    <div class="wsp_perfil_sub">{{ auth()->user()->email ?? '' }}</div>
                </div>
                <div class="wsp_filtro_seccion" style="padding: 16px;">
                    <p style="font-size:14px; color:var(--wsp-texto-sec);">
                        Sesión activa como agente del CRM WhatsApp Aybar.
                    </p>
                </div>
            </div>
        </div>

        {{-- ─ SIDEBAR: Nuevo Chat ─────────────────────────────────────────── --}}
        <div class="wsp_sidebar_izq {{ $sidebarNuevoChat ? 'visible' : '' }}">
            <div class="wsp_sidebar_cabecera_verde">
                <i class="fa-solid fa-arrow-left wsp_sidebar_icon_back_blanco"
                   wire:click="cerrarNuevoChat"></i>
                <span>Nuevo chat</span>
            </div>
            <div class="wsp_sidebar_cuerpo wsp_scroll">
                <div class="wsp_buscar_box" style="padding:12px 16px;">
                    <div class="wsp_buscar_input_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="wsp_buscar_input"
                               placeholder="Buscar nombre o número...">
                    </div>
                </div>
                <div style="padding:20px; text-align:center; color:var(--wsp-texto-sec); font-size:13px;">
                    <i class="fa-solid fa-users" style="font-size:40px; opacity:.2; display:block; margin-bottom:10px;"></i>
                    Los contactos disponibles aparecerán aquí.
                </div>
            </div>
        </div>

        {{-- ─ SIDEBAR: Filtro Avanzado ────────────────────────────────────── --}}
        <div class="wsp_sidebar_izq {{ $sidebarFiltro ? 'visible' : '' }}">
            <div class="wsp_sidebar_cabecera_verde">
                <i class="fa-solid fa-sliders wsp_sidebar_icon_back_blanco"></i>
                <span>Filtro avanzado</span>
                <button onclick="__x = document.querySelector('[wire\\:id]')"
                        style="margin-left:auto; background:none; border:none; color:white; cursor:pointer; font-size:18px;"
                        wire:click="cerrarFiltro" title="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="wsp_sidebar_cuerpo wsp_scroll">
                <div class="wsp_filtro_body" x-data="{
                    leido: '0', dept: '', fecha_inicio: '', fecha_fin: ''
                }">
                    {{-- Característica --}}
                    <div class="wsp_filtro_seccion">
                        <h5>Mensajes</h5>
                        <div class="wsp_filtro_options">
                            <label>
                                <input type="radio" x-model="leido" value="0"> Todos
                            </label>
                            <label>
                                <input type="radio" x-model="leido" value="1"> No leídos
                            </label>
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="wsp_filtro_seccion">
                        <h5>Fecha del último mensaje</h5>
                        <div class="wsp_filtro_options" style="flex-direction:column;">
                            <label>Desde: <input type="date" x-model="fecha_inicio"></label>
                            <label>Hasta: <input type="date" x-model="fecha_fin"></label>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="wsp_filtro_botones">
                        <button class="wsp_btn_outline"
                                wire:click="cerrarFiltro"
                                x-on:click="leido='0'; dept=''; fecha_inicio=''; fecha_fin=''">
                            Borrar
                        </button>
                        <button class="wsp_btn_primary"
                                x-on:click="
                                    $wire.cerrarFiltro();
                                    $dispatch('aplicarFiltroAvanzado', {leido, dept, fecha_inicio, fecha_fin})
                                ">
                            Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- fin wsp_panel_izq --}}

    {{-- ══════════════════════════════════════════════════════════════════════
        PANEL DERECHO
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_panel_der">

        {{-- Área principal conversación --}}
        <div style="flex:1; display:flex; flex-direction:column; height:100%; overflow:hidden; min-width:0;">
            @livewire('crm.whatsapp.chat-conversacion', [
                'sidebarBuscarMensajes' => $sidebarBuscarMensajes,
                'sidebarPlantillas'     => $sidebarPlantillas,
                'sidebarInfoContacto'   => $sidebarInfoContacto,
            ])
        </div>

        {{-- ─ SIDEBAR DERECHO: Buscar en chat ────────────────────────────── --}}
        <div class="wsp_sidebar_der {{ $sidebarBuscarMensajes ? 'visible' : '' }}">
            <div class="wsp_sidebar_der_cab">
                <i class="fa-solid fa-xmark wsp_sidebar_icon_back"
                   wire:click="abrirBuscar" title="Cerrar"></i>
                <span>Buscar mensajes</span>
            </div>
            <div class="wsp_sidebar_der_cuerpo wsp_scroll">
                <div class="wsp_buscar_chat_input">
                    <input type="text"
                           placeholder="Buscar en esta conversación..."
                           wire:model.live.debounce.300ms="conversacionActivaId"
                           style="pointer-events:none; display:none;">
                    {{-- Pasamos el search al ChatConversacion vía JS/Livewire events --}}
                    <input type="text" id="wsp-buscar-chat"
                           placeholder="Buscar texto..."
                           x-data
                           x-on:input.debounce.400ms="$dispatch('buscarEnChat', {q: $el.value})">
                </div>
                <div style="padding:20px; text-align:center; color:var(--wsp-texto-sec); font-size:13px;">
                    Los resultados aparecerán en el chat resaltados.
                </div>
            </div>
        </div>

        {{-- ─ SIDEBAR DERECHO: Plantillas ────────────────────────────────── --}}
        <div class="wsp_sidebar_der {{ $sidebarPlantillas ? 'visible' : '' }}">
            <div class="wsp_sidebar_der_cab">
                <i class="fa-solid fa-xmark wsp_sidebar_icon_back"
                   wire:click="abrirPlantillas"></i>
                <span>Plantillas</span>
            </div>
            <div class="wsp_sidebar_der_cuerpo wsp_scroll">
                {{-- La lista la gestiona ChatConversacion porque ya tiene los datos --}}
                <div style="padding:10px 12px; border-bottom:1px solid var(--wsp-borde);">
                    <div class="wsp_buscar_input_wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="wsp_buscar_input"
                               placeholder="Buscar plantilla..."
                               x-data
                               x-on:input.debounce.300ms="$dispatch('buscarPlantilla', {q: $el.value})">
                    </div>
                </div>
                <div style="padding:12px; color:var(--wsp-texto-sec); font-size:13px; text-align:center;">
                    <i class="fa-solid fa-bolt" style="color:var(--wsp-verde); margin-right:4px;"></i>
                    Selecciona una plantilla en la conversación.
                    <br><small style="margin-top:8px; display:block;">(Usa el icono ⚡ en la cabecera del chat)</small>
                </div>
            </div>
        </div>

        {{-- ─ SIDEBAR DERECHO: Info del contacto ─────────────────────────── --}}
        <div class="wsp_sidebar_der {{ $sidebarInfoContacto ? 'visible' : '' }}">
            <div class="wsp_sidebar_der_cab">
                <i class="fa-solid fa-xmark wsp_sidebar_icon_back"
                   wire:click="abrirInfoContacto"></i>
                <span>Info. del contacto</span>
            </div>
            <div class="wsp_sidebar_der_cuerpo wsp_scroll">
                @if($conversacionActivaId)
                    @php
                        $conv = \App\Models\WhatsappConversacion::with('contacto.cliente')
                            ->find($conversacionActivaId);
                    @endphp
                    @if($conv)
                        <div style="padding:24px; text-align:center; background:var(--wsp-fondo-panel);">
                            @php
                                $nomInfo = $conv->contacto->cliente
                                    ? $conv->contacto->cliente->nombre
                                    : ($conv->contacto->nombre_wa ?? $conv->contacto->wa_id);
                                $infoIni = strtoupper(substr($nomInfo, 0, 1));
                                $infoCols = ['#00a884','#0c7abf','#7b4ea0','#e06c1b','#b82143','#1b8a6b'];
                                $infoCol  = $infoCols[ord($infoIni) % count($infoCols)];
                            @endphp
                            <div class="wsp_perfil_avatar_grande" style="margin:0 auto 12px; background:{{ $infoCol }}; width:80px; height:80px; font-size:2rem;">
                                {{ $infoIni }}
                            </div>
                            <div class="wsp_perfil_nombre">{{ $nomInfo }}</div>
                        </div>
                        <div class="wsp_info_section">
                            <h5>WHATSAPP</h5>
                            <div class="wsp_info_row">
                                {{ $conv->contacto->wa_id }}
                                <span>Número WA</span>
                            </div>
                        </div>
                        @if($conv->contacto->cliente)
                        <div class="wsp_info_section">
                            <h5>CLIENTE ERP</h5>
                            <div class="wsp_info_row">
                                {{ $conv->contacto->cliente->nombre }}
                                <span>Nombre</span>
                            </div>
                        </div>
                        @endif
                        <div class="wsp_info_section">
                            <h5>CONVERSACIÓN</h5>
                            <div class="wsp_info_row">
                                {{ strtoupper($conv->departamento_destino ?? 'Sin departamento') }}
                                <span>Departamento</span>
                            </div>
                            <div class="wsp_info_row" style="margin-top:8px;">
                                {{ $conv->last_message_at?->format('d/m/Y H:i') ?? '—' }}
                                <span>Último mensaje</span>
                            </div>
                        </div>
                    @endif
                @else
                    <div style="padding:30px; text-align:center; color:var(--wsp-texto-sec); font-size:13px;">
                        Selecciona una conversación.
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- fin wsp_panel_der --}}

    {{-- ══════════════════════════════════════════════════════════════════════
        MODAL: Visualizador Multimedia
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($modalMultimedia)
    <div class="wsp_modal_multimedia" wire:click.self="cerrarMultimedia">
        <div class="wsp_modal_multimedia_cab">
            <button wire:click="cerrarMultimedia" title="Cerrar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="wsp_modal_multimedia_cuerpo">
            @if($mediaTipo === 'image' || $mediaTipo === 'sticker')
                <img src="{{ $mediaUrl }}" alt="Multimedia">
            @elseif($mediaTipo === 'video')
                <video controls autoplay>
                    <source src="{{ $mediaUrl }}">
                </video>
            @endif
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
        MODAL: Pausar Chat
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($modalPausar)
    <div class="wsp_modal_overlay" wire:click.self="cerrarPausar">
        <div class="wsp_modal_card">
            @if(!$chatPausado)
                <h5>Motivo para pausar el chat</h5>
                <div class="wsp_modal_radio_list">
                    @foreach(['En reunión', 'Fuera de oficina', 'Almuerzo / descanso', 'Otro motivo'] as $motivo)
                        <label>
                            <input type="radio" wire:model="motivoPausaSeleccionado" value="{{ $motivo }}">
                            {{ $motivo }}
                        </label>
                    @endforeach
                </div>
                <div class="wsp_modal_actions">
                    <button class="wsp_btn_outline" wire:click="cerrarPausar">Cancelar</button>
                    <button class="wsp_btn_primary" wire:click="confirmarPausa"
                            @if(!$motivoPausaSeleccionado) disabled @endif>
                        Pausar
                    </button>
                </div>
            @else
                <h5>Chat pausado</h5>
                <div class="wsp_modal_radio_list" style="margin-bottom:16px;">
                    <label>
                        <input type="radio" checked readonly>
                        {{ $motivoPausaSeleccionado }}
                    </label>
                </div>
                <div class="wsp_modal_actions">
                    <button class="wsp_btn_primary" wire:click="reanudarChat">
                        <i class="fa-solid fa-play" style="margin-right:6px;"></i>Activar
                    </button>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>{{-- fin wsp_layout --}}