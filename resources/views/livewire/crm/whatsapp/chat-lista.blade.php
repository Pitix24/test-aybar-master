{{-- =======================================================================
CHAT LISTA — Panel Izquierdo
Equivale a: cabecera-izquierda + buscar-izquierda + lista-chat-izquierda
======================================================================= --}}
<div style="display:flex; flex-direction:column; height:100%; background:var(--wsp-fondo-blanco);">

    {{-- ── CABECERA IZQUIERDA ── --}}
    <div class="wsp_cabecera_izq">
        {{-- Avatar del agente --}}
        <div class="wsp_avatar_propio" title="{{ auth()->user()->name ?? 'Agente' }}"
            wire:click="$dispatch('abrirSidebarPerfil')">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>

        {{-- Acciones de cabecera --}}
        <div class="wsp_cabecera_acciones">
            {{-- Pausar chat (muestra el modal en el container) --}}
            <div class="wsp_icon_btn {{ $chatPausado ?? false ? 'activo' : '' }}"
                title="{{ $chatPausado ?? false ? 'Chat pausado — clic para reanudar' : 'Pausar chat' }}"
                wire:click="$dispatch('abrirModalPausar')">
                <i class="fa-solid {{ $chatPausado ?? false ? 'fa-pause' : 'fa-play' }}"></i>
            </div>

            {{-- Nuevo chat --}}
            <div class="wsp_icon_btn" title="Nuevo chat" wire:click="$dispatch('abrirSidebarNuevoChat')">
                <i class="fa-solid fa-message-plus"></i>
            </div>

            {{-- Filtro avanzado --}}
            <div class="wsp_icon_btn" title="Filtro avanzado" wire:click="$dispatch('abrirSidebarFiltro')">
                <i class="fa-solid fa-filter"></i>
            </div>

            {{-- Opciones --}}
            <div class="wsp_icon_btn" title="Más opciones" x-data="{ open: false }" @click.stop="open = !open"
                @click.outside="open = false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
                {{-- Mini menú --}}
                <div x-show="open" x-transition style="position:absolute; top:36px; right:0; background:white; border-radius:4px;
                            box-shadow:var(--wsp-sombra); min-width:180px; z-index:200; padding:4px 0;">
                    <div style="padding:10px 20px; font-size:14px; cursor:pointer; color:var(--wsp-texto-ppal);"
                        @click="open=false" class="contenedor_opciones_item_hover">
                        <i class="fa-solid fa-user" style="margin-right:8px;"></i>Perfil
                    </div>
                    <div style="padding:10px 20px; font-size:14px; cursor:pointer; color:var(--wsp-texto-ppal);"
                        @click="open=false" class="contenedor_opciones_item_hover">
                        <i class="fa-solid fa-star" style="margin-right:8px;"></i>Mensajes destacados
                    </div>
                    <div style="padding:10px 20px; font-size:14px; cursor:pointer; color:var(--wsp-texto-ppal);"
                        @click="open=false" class="contenedor_opciones_item_hover">
                        <i class="fa-solid fa-gear" style="margin-right:8px;"></i>Configuración
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── BUSCADOR ── --}}
    <div class="wsp_buscar_box">
        <div class="wsp_buscar_input_wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" class="wsp_buscar_input" id="wsp-search-input"
                placeholder="Busca un chat o inicia uno nuevo" wire:model.live.debounce.300ms="search">
            @if($search)
                <i class="fa-solid fa-xmark" style="cursor:pointer; color:var(--wsp-icon-sec);"
                    wire:click="$set('search', '')" title="Limpiar"></i>
            @endif
        </div>
    </div>

    {{-- ── FILTROS RÁPIDOS (chips) ── --}}
    <div class="wsp_filtros_rapidos">
        <span class="wsp_filtro_chip {{ $filtro === null ? 'activo' : '' }}" wire:click="setFiltro(null)">Todos</span>
        <span class="wsp_filtro_chip {{ $filtro === 'no_leidos' ? 'activo' : '' }}"
            wire:click="setFiltro('no_leidos')">No leídos</span>
    </div>

    {{-- ── LISTA DE CONVERSACIONES ── --}}
    <div class="wsp_lista_chats wsp_scroll" wire:poll.8s>

        {{-- Item archivados (decorativo, como el Angular original) --}}
        <div class="wsp_chat_item" style="height:50px; background:var(--wsp-fondo-blanco);">
            <div class="wsp_chat_avatar"
                style="width:36px; height:36px; font-size:1rem; background:rgba(0,168,132,.12);">
                <i class="fa-solid fa-box-archive" style="color:var(--wsp-verde); font-size:14px;"></i>
            </div>
            <div class="wsp_chat_info">
                <div class="wsp_chat_row1">
                    <span class="wsp_chat_nombre" style="color:var(--wsp-verde);">Archivados</span>
                    <span class="wsp_badge_no_leido">2</span>
                </div>
            </div>
        </div>

        @forelse($conversaciones as $conv)
            @php
                $nombre = $conv->contacto->cliente
                    ? $conv->contacto->cliente->nombre
                    : ($conv->contacto->nombre_wa ?? $conv->contacto->wa_id);

                $inicial = strtoupper(substr($nombre, 0, 1));

                $ultimoMsg = $conv->mensajes->first();

                // Preview del último mensaje
                if ($ultimoMsg) {
                    $preview = match ($ultimoMsg->tipo) {
                        'image' => '📷 Foto',
                        'video' => '🎥 Video',
                        'audio', 'voice' => '🎤 Audio',
                        'document' => '📄 Documento',
                        'sticker' => '😀 Sticker',
                        'template' => '⚡ Plantilla',
                        default => \Illuminate\Support\Str::limit($ultimoMsg->contenido, 38),
                    };
                } else {
                    $preview = '<em style="font-style:italic;">Sin mensajes</em>';
                }

                $hora = $conv->last_message_at
                    ? ($conv->last_message_at->isToday()
                        ? $conv->last_message_at->format('H:i')
                        : ($conv->last_message_at->isYesterday()
                            ? 'Ayer'
                            : $conv->last_message_at->format('d/m/y'))
                    )
                    : '';

                $esActivo = $conversacionActivaId === $conv->id;
                $tieneNoLeer = $conv->mensajes_sin_leer > 0;

                $colores = ['#00a884', '#0c7abf', '#7b4ea0', '#e06c1b', '#b82143', '#1b8a6b'];
                $colorAvt = $colores[ord($inicial) % count($colores)];
            @endphp

            <div id="conv-item-{{ $conv->id }}" class="wsp_chat_item {{ $esActivo ? 'activo' : '' }}"
                wire:click="seleccionarConversacion({{ $conv->id }})">
                {{-- Avatar con inicial y color único --}}
                <div class="wsp_chat_avatar" style="background: {{ $colorAvt }};">
                    {{ $inicial }}
                </div>

                {{-- Detalle --}}
                <div class="wsp_chat_info">
                    <div class="wsp_chat_row1">
                        <span class="wsp_chat_nombre">{{ $nombre }}</span>
                        <span class="wsp_chat_hora {{ $tieneNoLeer ? 'no_leido' : '' }}">{{ $hora }}</span>
                    </div>
                    <div class="wsp_chat_row2">
                        <span class="wsp_chat_preview">
                            @if($ultimoMsg && $ultimoMsg->direccion === 'saliente')
                                <i class="fa-solid fa-check-double"
                                    style="font-size:11px; margin-right:2px; color:{{ $ultimoMsg->estado === 'leido' ? '#53bdeb' : 'var(--wsp-icon-sec)' }};"></i>
                            @endif
                            {!! $preview !!}
                        </span>
                        @if($tieneNoLeer)
                            <span class="wsp_badge_no_leido">{{ $conv->mensajes_sin_leer }}</span>
                        @endif
                    </div>
                </div>
            </div>

        @empty
            <div class="wsp_chat_vacio">
                <i class="fa-solid fa-comment-slash"
                    style="font-size:32px; opacity:.25; display:block; margin-bottom:12px;"></i>
                @if($search)
                    No se encontraron chats con "{{ $search }}"
                @else
                    No hay conversaciones disponibles.
                @endif
            </div>
        @endforelse

    </div>

</div>