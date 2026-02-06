<div class="g_chat_container">
    <div class="g_chat_overlay {{ $isOpen ? 'visible' : '' }}" wire:click="toggle"></div>

    <div class="g_chat_drawer {{ $isOpen ? 'open' : '' }}">
        <div class="g_chat_header">
            <h3>
                <i class="fa-solid fa-comments"></i>
                Mensajes del Ticket #{{ $ticket->id }}
                @if($es_interno)
                    <span
                        style="font-size: 0.7rem; background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 10px; border: 1px solid #f59e0b;">Nota
                        Interna</span>
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
                        {{ $msg->mensaje }}

                        @foreach($msg->archivos as $archivo)
                            <a href="{{ Storage::url($archivo->path) }}" target="_blank" class="g_chat_attachment_preview"
                                style="color: inherit; text-decoration: none;">
                                <i class="fa-solid fa-paperclip"></i>
                                <span>{{ $archivo->nombre_original }}</span>
                            </a>
                        @endforeach

                        <div class="g_message_time">
                            {{ $msg->created_at->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="g_vacio">
                    <i class="fa-solid fa-message-slash" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                    <p>No hay mensajes aún. Comienza la conversación.</p>
                </div>
            @endforelse
        </div>

        <div class="g_chat_footer">
            <div class="g_chat_input_container">
                <div class="g_chat_input_wrapper">
                    <textarea wire:model="mensaje" placeholder="Escribe un mensaje..." rows="1"
                        oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>

                    <div class="g_chat_actions">
                        <label class="g_chat_action_btn" title="Adjuntar archivo">
                            <i class="fa-solid fa-paperclip"></i>
                            <input type="file" wire:model="adjunto" style="display: none;">
                        </label>

                        <button class="g_chat_action_btn {{ $es_interno ? 'text-orange-500' : '' }}"
                            wire:click="$toggle('es_interno')"
                            title="{{ $es_interno ? 'Desactivar nota interna' : 'Activar como nota interna' }}">
                            <i class="fa-solid {{ $es_interno ? 'fa-lock' : 'fa-lock-open' }}"></i>
                        </button>

                        @if($adjunto)
                            <div style="font-size: 0.75rem; color: #3b82f6; display: flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-file-circle-check"></i>
                                {{ Str::limit($adjunto->getClientOriginalName(), 15) }}
                                <i class="fa-solid fa-times" style="cursor: pointer; color: #ef4444;"
                                    wire:click="$set('adjunto', null)"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <button class="g_chat_send_btn" wire:click="enviar" wire:loading.attr="disabled">
                    <i class="fa-solid fa-paper-plane" wire:loading.remove wire:target="enviar"></i>
                    <i class="fa-solid fa-spinner fa-spin" wire:loading wire:target="enviar"></i>
                </button>
            </div>
        </div>
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
            setTimeout(scrollToBottom, 100);
        });

        $wire.on('mensajeEnviado', () => {
            setTimeout(scrollToBottom, 50);
        });

        // Polling para nuevos mensajes (opcional si no hay websockets)
        // setInterval(() => { $wire.$refresh(); }, 5000);
    </script>
    @endscript
</div>