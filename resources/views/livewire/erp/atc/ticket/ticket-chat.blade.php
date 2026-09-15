<div class="g_chat_container">
    <div class="g_chat_overlay {{ $isOpen ? 'visible' : '' }}" wire:click="toggle"></div>

    <div class="g_chat_drawer {{ $isOpen ? 'open' : '' }}">
        <div class="g_chat_header">
            <h3>
                <i class="fa-solid fa-comments"></i>
                Mensajes del Ticket #{{ $ticket->id }}
                @if($es_interno)
                    <span>Nota Interna</span>
                @endif
            </h3>
            <button class="g_chat_close" wire:click="toggle">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="g_chat_body" id="chat-body">
            @forelse($mensajes as $msg)
                        @php
                            $isMine = $msg->user_id === auth()->id();
                        @endphp
                        <div
                            class="g_message {{ $isMine ? 'g_message_sent' : 'g_message_received' }} {{ $msg->es_interno ? 'g_message_internal' : '' }}">
                            <div class="g_message_user_info">
                                <strong>{{ $msg->user->name }}</strong>
                                @if($msg->es_interno) <i class="fa-solid fa-lock" title="Solo visible para admin"></i> @endif
                            </div>
                            <div class="g_message_content">
                                {!! preg_replace(
                    '~(https?://[^\s<]+)~i',
                    '<a href="$1" target="_blank" style="text-decoration: underline; color: inherit; font-weight: bold;">$1</a>',
                    e($msg->mensaje)
                ) !!}

                                <div class="g_message_time">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
            @empty
                <div class="g_vacio">
                    <i class="fa-solid fa-message-slash"></i>
                    <p>No hay mensajes aún. Comienza la conversación.</p>
                </div>
            @endforelse
        </div>

        @if(!$soloLectura)
            <div class="g_chat_footer">
                <div class="g_chat_input_container">
                    <div class="g_chat_input_wrapper">
                        <textarea wire:model="mensaje" placeholder="Escribe un mensaje..." rows="1"
                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>

                        <div class="g_chat_actions">
                            <button class="g_chat_action_btn {{ $es_interno ? 'g_chat_action_btn_active' : '' }}"
                                wire:click="$toggle('es_interno')"
                                title="{{ $es_interno ? 'Desactivar nota interna' : 'Activar como nota interna' }}">
                                <i class="fa-solid {{ $es_interno ? 'fa-lock' : 'fa-lock-open' }}"></i>
                            </button>
                        </div>
                    </div>

                    <button class="g_chat_send_btn" wire:click="enviar" wire:loading.attr="disabled">
                        <i class="fa-solid fa-paper-plane" wire:loading.remove wire:target="enviar"></i>
                        <i class="fa-solid fa-spinner fa-spin" wire:loading wire:target="enviar"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    @script
    <script>
        const scrollToBottom = () => {
            const chatBody = document.getElementById('chat-body');
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        };

        $wire.on('chatOpened', () => {
            setTimeout(scrollToBottom, 50);
        });

        $wire.on('mensajeEnviado', () => {
            setTimeout(scrollToBottom, 50);
        });
    </script>
    @endscript
</div>