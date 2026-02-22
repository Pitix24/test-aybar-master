<div class="chat-conversacion-container" wire:poll.5s
    style="height: 100%; display: flex; flex-direction: column; background-color: #f0f2f5;">
    @if($conversacion)
        <!-- Cabecera del Chat -->
        <div class="chat-header"
            style="padding: 10px 20px; background: #fff; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center;">
                <div class="avatar"
                    style="width: 40px; height: 40px; background: #00a884; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px; font-weight: bold;">
                    {{ substr($conversacion->cliente->nombre, 0, 1) }}
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 16px;">{{ $conversacion->cliente->nombre }}</h4>
                    <span style="font-size: 12px; color: #666;">{{ $conversacion->cliente->telefono_principal }}</span>
                </div>
            </div>
            <div class="chat-actions">
                <span class="badge"
                    style="background: #e9edef; padding: 5px 10px; border-radius: 10px; font-size: 11px; color: #54656f;">
                    {{ strtoupper($conversacion->departamento_destino ?? 'PENDIENTE') }}
                </span>
            </div>
        </div>

        <!-- Cuerpo de Mensajes -->
        <div id="chat-body" class="chat-body"
            style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 8px;"
            x-data="{ scrollToBottom() { $el.scrollTop = $el.scrollHeight } }" x-init="scrollToBottom()"
            @mensaje-enviado.window="setTimeout(() => scrollToBottom(), 50)">
            @foreach($mensajes as $mensaje)
                <div class="message-wrapper {{ $mensaje->direccion === 'saliente' ? 'message-sent' : 'message-received' }}"
                    style="display: flex; {{ $mensaje->direccion === 'saliente' ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">

                    <div class="message-bubble"
                        style="max-width: 65%; padding: 8px 12px; border-radius: 8px; position: relative; font-size: 14px; 
                                         {{ $mensaje->direccion === 'saliente' ? 'background: #d9fdd3; border-radius: 8px 0 8px 8px;' : 'background: #fff; border-radius: 0 8px 8px 8px; box-shadow: 0 1px 0.5px rgba(11,20,26,.13);' }}">

                        <div class="message-content">
                            @if($mensaje->tipo === 'texto')
                                {{ $mensaje->contenido }}
                            @elseif($mensaje->tipo === 'image')
                                <img src="{{ $mensaje->contenido }}" style="max-width: 100%; border-radius: 4px;">
                            @endif
                        </div>

                        <div class="message-meta"
                            style="display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px; font-size: 11px; color: #667781;">
                            {{ $mensaje->created_at->format('H:i') }}

                            @if($mensaje->direccion === 'saliente')
                                @if($mensaje->estado === 'enviado')
                                    <span>✓</span>
                                @elseif($mensaje->estado === 'entregado')
                                    <span>✓✓</span>
                                @elseif($mensaje->estado === 'leido')
                                    <span style="color: #53bdeb;">✓✓</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Área de Input -->
        <div class="chat-footer" style="padding: 10px 20px; background: #f0f2f5;">
            <form wire:submit.prevent="enviarMensaje" style="display: flex; gap: 10px;">
                <input type="text" wire:model.defer="nuevoMensaje" placeholder="Escribe un mensaje aquí"
                    style="flex: 1; border: none; padding: 10px 15px; border-radius: 8px; outline: none; font-size: 15px;">
                <button type="submit"
                    style="background: #00a884; border: none; color: white; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                    ENVIAR
                </button>
            </form>
        </div>
    @else
        <div
            style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #667781;">
            <img src="https://abs.twimg.com/emoji/v2/72x72/1f4ac.png"
                style="width: 80px; opacity: 0.5; margin-bottom: 20px;">
            <h3>Bienvenido al CRM de WhatsApp</h3>
            <p>Selecciona un cliente de la lista para comenzar a conversar.</p>
        </div>
    @endif
</div>