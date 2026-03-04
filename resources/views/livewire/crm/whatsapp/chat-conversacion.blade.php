{{-- =======================================================================
    CHAT CONVERSACION — Panel Derecho
    Equivale a: cabecera-derecha + caja-mensajes + mensaje-derecha + chat-input
    Incluye emoticones, herramientas, plantillas, modal multimedia interno
    ======================================================================= --}}
<div class="wsp_conv_area"
     x-data="wspConv()"
     x-init="initScroll()"
     @mensaje-enviado.window="setTimeout(() => scrollToBottom(), 100)"
     @keydown.escape.window="mostrarEmoticones = false; mostrarHerramientas = false;">

@if($conversacion)

    {{-- ═══════════════════════════════════════════════════════════════════
        BANNER: CHAT PAUSADO
    ═══════════════════════════════════════════════════════════════════ --}}
    {{-- Nota: el estado de pausa viene del ChatContainer. Se puede pasar como prop si se necesita --}}

    {{-- ═══════════════════════════════════════════════════════════════════
        CABECERA
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_cabecera_der">
        {{-- Info del contacto (clic abre sidebar info) --}}
        <div class="wsp_cabecera_der_info" wire:click="$dispatch('abrirSidebarInfoContacto')">
            @php
                $nombre = $conversacion->contacto->cliente
                    ? $conversacion->contacto->cliente->nombre
                    : ($conversacion->contacto->nombre_wa ?? $conversacion->contacto->wa_id);
                $inicial  = strtoupper(substr($nombre, 0, 1));
                $colores  = ['#00a884','#0c7abf','#7b4ea0','#e06c1b','#b82143','#1b8a6b'];
                $colorAvt = $colores[ord($inicial) % count($colores)];
            @endphp

            <div class="wsp_chat_avatar"
                 style="background:{{ $colorAvt }}; width:40px; height:40px; font-size:1rem; flex-shrink:0;">
                {{ $inicial }}
            </div>
            <div class="wsp_cabecera_der_texto">
                <h4>{{ $nombre }}</h4>
                <span>{{ $conversacion->contacto->wa_id }}</span>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="wsp_cabecera_acciones">
            @if($conversacion->departamento_destino)
                <span class="wsp_dept_badge">
                    {{ strtoupper($conversacion->departamento_destino) }}
                </span>
            @endif

            {{-- Buscar en chat --}}
            <div class="wsp_icon_btn {{ $sidebarBuscarMensajes ? 'activo' : '' }}"
                 title="Buscar mensajes"
                 wire:click="$dispatch('abrirSidebarBuscar')">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>

            {{-- Plantillas rápidas ⚡ --}}
            <div class="wsp_icon_btn {{ $sidebarPlantillas ? 'activo' : '' }}"
                 title="Plantillas de mensajes"
                 wire:click="$dispatch('abrirSidebarPlantillas')">
                <i class="fa-solid fa-bolt"></i>
            </div>

            {{-- Opciones del chat --}}
            <div class="wsp_icon_btn" title="Más opciones"
                 x-data="{ open: false }" @click.stop="open = !open" @click.outside="open = false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
                <div x-show="open" x-transition
                     style="position:absolute; top:36px; right:0; background:white; border-radius:4px;
                            box-shadow:var(--wsp-sombra); min-width:200px; z-index:200; padding:4px 0;">
                    @foreach(['Info. del contacto','Seleccionar mensajes','Silenciar notificaciones','Mensajes temporales','Eliminar chat','Reportar','Bloquear'] as $opcion)
                    <div style="padding:11px 20px; font-size:14px; cursor:pointer; color:var(--wsp-texto-ppal);"
                         class="contenedor_opciones_item_hover">{{ $opcion }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
        PANEL PLANTILLAS (deslizante, sobre el cuerpo de mensajes)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($sidebarPlantillas)
    <div style="border-bottom:1px solid var(--wsp-borde); background:var(--wsp-fondo-blanco); max-height:220px; display:flex; flex-direction:column;">
        <div class="wsp_sidebar_der_cab" style="border-bottom:1px solid var(--wsp-borde);">
            <i class="fa-solid fa-bolt" style="color:var(--wsp-verde);"></i>
            <span>Plantillas rápidas</span>
            <div style="margin-left:auto;">
                <input type="text"
                       wire:model.live.debounce.300ms="buscarPlantilla"
                       placeholder="Buscar..."
                       style="padding:5px 10px; border:1px solid var(--wsp-borde-fuerte); border-radius:6px; font-size:13px; outline:none; width:160px;">
            </div>
        </div>
        <div class="wsp_scroll" style="overflow-y:auto; flex:1;">
            @forelse($plantillas as $pt)
                <div class="wsp_plantilla_item" wire:click="usarPlantilla({{ $pt->id }})">
                    <span class="wsp_plantilla_badge">{{ $pt->categoria ?? 'General' }}</span>
                    <div class="wsp_plantilla_texto">{{ Str::limit($pt->contenido, 80) }}</div>
                    <button class="wsp_plantilla_btn">Elegir</button>
                </div>
            @empty
                <div style="padding:20px; text-align:center; color:var(--wsp-texto-sec); font-size:13px;">
                    No hay plantillas disponibles.
                </div>
            @endforelse
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
        CUERPO DE MENSAJES
    ═══════════════════════════════════════════════════════════════════ --}}
    <div id="wsp-chat-body"
         class="wsp_cuerpo_mensajes wsp_scroll"
         wire:poll.6s
         x-ref="chatBody">

        @php $fechaAnt = null; @endphp

        @foreach($mensajes as $mensaje)
            @php
                $fechaMsg    = $mensaje->created_at->format('Y-m-d');
                $mostrarFec  = $fechaMsg !== $fechaAnt;
                $fechaAnt    = $fechaMsg;
                $esSaliente  = $mensaje->direccion === 'saliente';
            @endphp

            {{-- Separador de fecha --}}
            @if($mostrarFec)
                <div class="wsp_fecha_separador">
                    @if($mensaje->created_at->isToday())
                        Hoy
                    @elseif($mensaje->created_at->isYesterday())
                        Ayer
                    @else
                        {{ $mensaje->created_at->translatedFormat('d \d\e F \d\e Y') }}
                    @endif
                </div>
            @endif

            {{-- Burbuja --}}
            <div class="wsp_mensaje_wrapper {{ $esSaliente ? 'saliente' : 'entrante' }}"
                 id="msg-{{ $mensaje->id }}">
                <div class="wsp_burbuja">

                    {{-- ── CONTENIDO POR TIPO ── --}}
                    @switch($mensaje->tipo)

                        @case('texto') @case('text')
                            <div style="overflow-wrap:anywhere; white-space:pre-wrap;">{{ $mensaje->contenido }}</div>
                            @break

                        @case('image') @case('imagen')
                            @php $imgUrl = $mensaje->contenido; @endphp
                            <img src="{{ $imgUrl }}" class="wsp_media_img" alt="Foto"
                                 wire:click="verMultimedia('{{ addslashes($imgUrl) }}', 'image')"
                                 title="Ver imagen">
                            @if($mensaje->caption ?? null)
                                <div style="font-size:13px; margin-top:3px;">{{ $mensaje->caption }}</div>
                            @endif
                            @break

                        @case('video')
                            <div style="position:relative; display:inline-block; cursor:pointer;"
                                 wire:click="verMultimedia('{{ addslashes($mensaje->contenido) }}', 'video')">
                                <video class="wsp_media_vid" preload="metadata">
                                    <source src="{{ $mensaje->contenido }}">
                                </video>
                                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
                                            background:rgba(0,0,0,.45); border-radius:50%; width:40px; height:40px;
                                            display:flex; align-items:center; justify-content:center;">
                                    <i class="fa-solid fa-play" style="color:white; font-size:14px; margin-left:3px;"></i>
                                </div>
                            </div>
                            @if($mensaje->caption ?? null)
                                <div style="font-size:13px; margin-top:3px;">{{ $mensaje->caption }}</div>
                            @endif
                            @break

                        @case('audio') @case('voice')
                            <audio class="wsp_media_audio" controls>
                                <source src="{{ $mensaje->contenido }}">
                            </audio>
                            @break

                        @case('document')
                            @php
                                $ext = strtolower(pathinfo($mensaje->contenido ?? '', PATHINFO_EXTENSION));
                                $docIcon = match($ext) {
                                    'pdf'        => '<i class="fa-solid fa-file-pdf" style="color:#F34646; font-size:24px;"></i>',
                                    'doc','docx' => '<i class="fa-solid fa-file-word" style="color:#60AAF6; font-size:24px;"></i>',
                                    'xls','xlsx' => '<i class="fa-solid fa-file-excel" style="color:#57D28B; font-size:24px;"></i>',
                                    'mp3'        => '<i class="fa-solid fa-file-audio" style="color:#FA6533; font-size:24px;"></i>',
                                    'ogg'        => '<i class="fa-solid fa-file-audio" style="color:#6F8171; font-size:24px;"></i>',
                                    'zip','rar'  => '<i class="fa-solid fa-file-zipper" style="color:#7F66FF; font-size:24px;"></i>',
                                    default      => '<i class="fa-solid fa-file" style="color:#79909B; font-size:24px;"></i>',
                                };
                            @endphp
                            <a href="{{ $mensaje->contenido }}" target="_blank" class="wsp_doc_link">
                                {!! $docIcon !!}
                                <div>
                                    <div style="font-size:13px; font-weight:500;">{{ $mensaje->nombre_archivo ?? basename($mensaje->contenido ?? 'documento') }}</div>
                                    <div style="font-size:11px; color:var(--wsp-texto-sec);">{{ strtoupper($ext) }}</div>
                                </div>
                            </a>
                            @break

                        @case('sticker')
                            <img src="{{ $mensaje->contenido }}"
                                 style="max-width:120px; max-height:120px; display:block;"
                                 wire:click="verMultimedia('{{ addslashes($mensaje->contenido) }}', 'sticker')"
                                 alt="Sticker">
                            @break

                        @case('template')
                            <div style="font-size:12px; color:var(--wsp-texto-sec); font-style:italic;">
                                <i class="fa-solid fa-bolt" style="color:var(--wsp-verde); margin-right:4px;"></i>
                                Plantilla: <strong style="color:var(--wsp-texto-ppal);">{{ $mensaje->contenido }}</strong>
                            </div>
                            @break

                        @default
                            <div style="overflow-wrap:anywhere;">{{ $mensaje->contenido }}</div>

                    @endswitch

                    {{-- Meta: hora + estado --}}
                    <div class="wsp_mensaje_meta">
                        <span>{{ $mensaje->created_at->format('H:i') }}</span>
                        @if($esSaliente)
                            @switch($mensaje->estado)
                                @case('enviando')
                                    <i class="fa-regular fa-clock wsp_tick" style="font-size:10px;" title="Enviando..."></i>
                                    @break
                                @case('enviado')
                                    <i class="fa-solid fa-check wsp_tick" title="Enviado"></i>
                                    @break
                                @case('entregado')
                                    <i class="fa-solid fa-check-double wsp_tick" title="Entregado"></i>
                                    @break
                                @case('leido')
                                    <i class="fa-solid fa-check-double wsp_tick leido" title="Leído"></i>
                                    @break
                                @case('fallido')
                                    <i class="fa-solid fa-circle-exclamation" style="color:#fb0000; font-size:12px;" title="Error al enviar"></i>
                                    @break
                            @endswitch
                        @endif
                    </div>

                </div>
            </div>

        @endforeach

        <div style="height:10px;"></div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
        PANEL DE EMOTICONES (flotante sobre el footer)
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_emoticones_panel" x-show="mostrarEmoticones" x-transition @click.stop>
        <div class="wsp_emoticones_tabs">
            <span title="Caritas">😀</span>
            <span title="Animales">🐶</span>
            <span title="Viajes">✈️</span>
            <span title="Actividades">⚽</span>
            <span title="Objetos">💡</span>
            <span title="Símbolos">❤️</span>
            <span title="Banderas">🏳️</span>
        </div>
        <div class="wsp_emoticones_buscar">
            <input type="text" x-model="emojiSearch" placeholder="Buscar emoji...">
        </div>
        <div class="wsp_emoticones_grid wsp_scroll">
            <template x-for="emoji in emojisFiltrados" :key="emoji">
                <span x-text="emoji" @click="insertarEmoji(emoji)"></span>
            </template>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
        PANEL DE HERRAMIENTAS (adjuntar archivos)
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_herramientas_panel" x-show="mostrarHerramientas" x-transition>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Imágenes y videos" style="background:var(--wsp-tool-blue);">
            <label style="cursor:pointer; display:flex;">
                <input type="file" accept="image/*,video/*" style="display:none;">
                <i class="fa-solid fa-image"></i>
            </label>
        </div>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Cámara" style="background:var(--wsp-tool-pink);">
            <i class="fa-solid fa-camera"></i>
        </div>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Contacto" style="background:var(--wsp-tool-cyan);">
            <i class="fa-solid fa-address-book"></i>
        </div>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Documento" style="background:var(--wsp-tool-purple);">
            <label style="cursor:pointer; display:flex;">
                <input type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip" style="display:none;">
                <i class="fa-solid fa-file"></i>
            </label>
        </div>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Encuesta" style="background:var(--wsp-tool-amber);">
            <i class="fa-solid fa-chart-bar"></i>
        </div>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Sticker" style="background:var(--wsp-tool-teal);">
            <i class="fa-solid fa-face-grin-stars"></i>
        </div>
        <div class="wsp_herramienta_btn wsp_tool_tooltip"
             data-tip="Plantillas" style="background:var(--wsp-tool-green);"
             wire:click="$dispatch('abrirSidebarPlantillas')">
            <i class="fa-solid fa-bolt"></i>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
        FOOTER / INPUT
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_footer">

        {{-- Emoticones --}}
        <div class="wsp_footer_icon" title="Emoticones"
             @click.stop="mostrarEmoticones = !mostrarEmoticones; mostrarHerramientas = false">
            <i class="fa-regular fa-face-smile" :class="{'fa-solid': mostrarEmoticones}"></i>
        </div>

        {{-- Herramientas --}}
        <div class="wsp_footer_icon" title="Adjuntar"
             @click.stop="mostrarHerramientas = !mostrarHerramientas; mostrarEmoticones = false">
            <i class="fa-solid fa-plus"></i>
        </div>

        {{-- Input de texto con Livewire --}}
        <form wire:submit.prevent="enviarMensaje"
              style="display:flex; flex:1; gap:8px; align-items:center; min-width:0;"
              @click="mostrarEmoticones = false; mostrarHerramientas = false">
            <textarea
                id="wsp-msg-input"
                class="wsp_mensaje_input wsp_scroll"
                wire:model.defer="nuevoMensaje"
                placeholder="Escribe un mensaje aquí"
                rows="1"
                x-ref="msgInput"
                x-on:keydown.enter.prevent="
                    if (!$event.shiftKey) {
                        $wire.enviarMensaje().then(() => {
                            $el.style.height = '42px';
                        });
                    } else {
                        const ta = $el;
                        const s = ta.selectionStart, e = ta.selectionEnd;
                        ta.value = ta.value.substring(0,s) + '\n' + ta.value.substring(e);
                        ta.selectionStart = ta.selectionEnd = s + 1;
                        autoResize(ta);
                    }
                "
                x-on:input="autoResize($el)"
                style="height:42px;"
            ></textarea>

            <button type="submit" class="wsp_send_btn" title="Enviar (Enter)">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        {{-- Micrófono --}}
        <div class="wsp_footer_icon" title="Grabar audio">
            <i class="fa-solid fa-microphone"></i>
        </div>

    </div>

@else

    {{-- ═══════════════════════════════════════════════════════════════════
        PANTALLA DE BIENVENIDA
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="wsp_bienvenida">
        <div class="wsp_bienvenida_icon">💬</div>
        <h2>WhatsApp Web — CRM Aybar</h2>
        <p>Selecciona un cliente de la lista izquierda para iniciar o continuar una conversación de forma rápida y segura.</p>
        <hr>
        <small>
            <i class="fa-solid fa-lock" style="font-size:11px;"></i>
            Cifrado de extremo a extremo
        </small>
    </div>

@endif

</div>

@push('scripts')
<script>
/**
 * Alpine.js component para estado local del chat:
 * - Scroll automático al fondo
 * - Emoticones (picker nativo unicode)
 * - Auto-resize del textarea
 */
function wspConv() {
    // Emojis agrupados (muestra 80+ emojis comunes)
    const EMOJIS_BASE = [
        '😀','😂','🥲','😍','🥰','😎','🤩','😅','😆','😊','🙂','😏','😒',
        '😢','😭','😤','😡','🤯','🥳','😴','🤔','🙄','😬','😱','🤗','🤫',
        '🫡','🤭','😶','😐','🫤','🙁','😟','😕','☹️','😮','😯','😲','😳',
        '👍','👎','👏','🙌','🫶','❤️','🧡','💛','💚','💙','💜','🖤','🤍',
        '🔥','⭐','✨','🎉','🎂','🎁','🏆','🥇','🚀','💡','💯','✅','❌',
        '🙏','💪','👊','✊','🤝','👋','🫂','🤞','🫰','✌️','🤙','👆','👇',
        '🐶','🐱','🐭','🐹','🐰','🦊','🐻','🐼','🐨','🦁','🐮','🐸','🐵',
        '🍕','🍔','🌮','🎂','🍰','🧁','🍩','🍪','☕','🧃','🍺','🥤','🍷',
        '⚽','🏀','🎾','🏊','🚴','✈️','🚗','🚀','🏖','🌍','🌟','🌈','☀️',
    ];

    return {
        mostrarEmoticones: false,
        mostrarHerramientas: false,
        emojiSearch: '',

        get emojisFiltrados() {
            if (!this.emojiSearch) return EMOJIS_BASE;
            // Filtro básico por nombre (no tenemos BD de nombres, así que devolvemos todo)
            return EMOJIS_BASE;
        },

        initScroll() {
            this.$nextTick(() => this.scrollToBottom());
        },

        scrollToBottom() {
            const el = this.$refs.chatBody;
            if (el) el.scrollTop = el.scrollHeight;
        },

        insertarEmoji(emoji) {
            // Inyecta el emoji en el textarea de Livewire
            const ta = this.$refs.msgInput;
            if (!ta) return;

            const start = ta.selectionStart ?? ta.value.length;
            const end   = ta.selectionEnd ?? ta.value.length;

            // Actualizar el valor nativo y disparar input para que Livewire lo recoja
            const nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLTextAreaElement.prototype, 'value').set;
            nativeInputValueSetter.call(ta, ta.value.substring(0, start) + emoji + ta.value.substring(end));
            ta.dispatchEvent(new Event('input', { bubbles: true }));

            ta.selectionStart = ta.selectionEnd = start + emoji.length;
            ta.focus();

            this.mostrarEmoticones = false;
        },
    };
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 180) + 'px';
}
</script>
@endpush