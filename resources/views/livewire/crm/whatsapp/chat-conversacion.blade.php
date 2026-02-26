<div class="g_whatsapp_container" wire:poll.5s>
    @if($conversacion)
        <!-- Cabecera del Chat -->
        <div class="g_whatsapp_header">
            <div class="g_whatsapp_header_info">
                <div class="g_whatsapp_avatar">
                    {{ substr($conversacion->contacto->nombre_wa ?? 'U', 0, 1) }}
                </div>
                <div class="g_whatsapp_header_text">
                    <h4>{{ $conversacion->contacto->nombre_wa ?? 'Usuario Desconocido' }}</h4>
                    <span>{{ $conversacion->contacto->wa_id }}</span>
                </div>
            </div>
            <div class="g_whatsapp_header_actions">
                <span class="g_badge light g_badge_soft">
                    {{ strtoupper($conversacion->departamento_destino ?? 'PENDIENTE') }}
                </span>
            </div>
        </div>

        <!-- Cuerpo de Mensajes -->
        <div id="chat-body" class="g_whatsapp_body" x-data="{ scrollToBottom() { $el.scrollTop = $el.scrollHeight } }"
            x-init="scrollToBottom()" @mensaje-enviado.window="setTimeout(() => scrollToBottom(), 50)">
            @foreach($mensajes as $mensaje)
                <div class="g_whatsapp_message_wrapper {{ $mensaje->direccion === 'saliente' ? 'sent' : 'received' }}">

                    <div class="g_whatsapp_bubble {{ $mensaje->direccion === 'saliente' ? 'sent' : 'received' }}">

                        <div class="g_whatsapp_message_content">
                            @if($mensaje->tipo === 'texto')
                                {{ $mensaje->contenido }}
                            @elseif($mensaje->tipo === 'image')
                                <img src="{{ $mensaje->contenido }}">
                            @endif
                        </div>

                        <div class="g_whatsapp_message_meta">
                            {{ $mensaje->created_at->format('H:i') }}

                            @if($mensaje->direccion === 'saliente')
                                @if($mensaje->estado === 'enviado')
                                    <span class="g_whatsapp_status">✓</span>
                                @elseif($mensaje->estado === 'entregado')
                                    <span class="g_whatsapp_status">✓✓</span>
                                @elseif($mensaje->estado === 'leido')
                                    <span class="g_whatsapp_status leido">✓✓</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Área de Input -->
        <div class="g_whatsapp_footer">
            <form wire:submit.prevent="enviarMensaje" class="g_whatsapp_input_form">
                <input type="text" wire:model.defer="nuevoMensaje" placeholder="Escribe un mensaje aquí"
                    class="g_whatsapp_input">
                <button type="submit" class="g_whatsapp_send_btn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    @else
        <div class="g_whatsapp_empty">
            <img src="https://abs.twimg.com/emoji/v2/72x72/1f4ac.png" alt="Chat icon">
            <h3>Bienvenido al CRM de Aybar</h3>
            <p>Selecciona un cliente de la lista para comenzar a conversar de forma rápida y segura.</p>
        </div>
    @endif
</div>