<div class="chat-lista-container" wire:poll.5s
    style="height: 100%; display: flex; flex-direction: column; background: #fff; border-right: 1px solid #ddd;">
    <!-- Buscador -->
    <div style="padding: 10px 15px; background: #fff; border-bottom: 1px solid #f0f2f5;">
        <input type="text" wire:model.live="search" placeholder="Buscar cliente..."
            style="width: 100%; background: #f0f2f5; border: none; padding: 8px 12px; border-radius: 8px; font-size: 14px; outline: none;">
    </div>

    <!-- Lista -->
    <div style="flex: 1; overflow-y: auto;">
        @forelse($conversaciones as $conv)
            <div wire:click="seleccionarConversacion({{ $conv->id }})" class="chat-item"
                style="display: flex; padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f0f2f5; transition: background 0.2s; align-items: center;"
                onmouseover="this.style.background='#f5f6f6'" onmouseout="this.style.background='transparent'">

                <div class="avatar"
                    style="width: 48px; height: 48px; min-width: 48px; background: #00a884; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">
                    @php
                        $nombreMostrar = $conv->contacto->cliente ? $conv->contacto->cliente->nombre : $conv->contacto->nombre_wa;
                    @endphp
                    {{ substr($nombreMostrar, 0, 1) }}
                </div>

                <div style="flex: 1; margin-left: 15px; overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-weight: 600; font-size: 15px; color: #111;">{{ $nombreMostrar }}</span>
                        <span style="font-size: 12px; color: #666;">
                            {{ $conv->last_message_at ? $conv->last_message_at->format('H:i') : '' }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2px;">
                        <p
                            style="margin: 0; font-size: 13px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            @if($conv->mensajes->isNotEmpty())
                                {{ $conv->mensajes->first()->contenido }}
                            @else
                                <span style="font-style: italic;">Sin mensajes</span>
                            @endif
                        </p>

                        @if($conv->mensajes_sin_leer > 0)
                            <span
                                style="background: #25d366; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                {{ $conv->mensajes_sin_leer }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="padding: 20px; text-align: center; color: #666;">
                No se encontraron contactos.
            </div>
        @endforelse
    </div>
</div>