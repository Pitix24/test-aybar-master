<div class="g_panel">
    <h4 class="g_panel_titulo">
        <i class="fa-solid fa-envelope"></i> Enviar Email al Cliente
    </h4>

    <div class="formulario">
        @if(!$ticket->email)
            <div class="g_alerta_error">
                <i class="fa-solid fa-circle-exclamation"></i>
                El cliente no tiene un email registrado en este ticket. No se puede realizar el envío.
            </div>
        @endif

        <div class="g_margin_bottom_10">
            <label>Destinatario:</label>
            <input type="text" value="{{ $ticket->email }}" disabled class="g_input_disabled"
                placeholder="No hay email registrado">
        </div>

        <div class="g_margin_bottom_10">
            <label>Asunto:</label>
            <input type="text" wire:model="asunto" placeholder="Escriba el asunto del correo...">
            @error('asunto') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <label>Mensaje:</label>
            <textarea wire:model="mensaje" rows="6"
                placeholder="Escriba el contenido del mensaje para el cliente..."></textarea>
            @error('mensaje') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <label><i class="fa-solid fa-paperclip"></i> Adjuntar archivos del Ticket:</label>
            <div
                style="max-height: 200px; overflow-y: auto; background: var(--fondo-gris); padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
                @forelse($archivosCompatibles as $archivo)
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <input type="checkbox" wire:model="selectedAttachments" value="{{ $archivo->id }}"
                            id="file-email-{{ $archivo->id }}" style="margin-right: 10px;">
                        <label for="file-email-{{ $archivo->id }}"
                            style="font-size: 13px; cursor: pointer; color: var(--texto-color);">
                            {{ $archivo->nombre_original }}
                            <span style="color: #888; font-size: 11px;">({{ number_format($archivo->size / 1024, 2) }}
                                KB)</span>
                        </label>
                    </div>
                @empty
                    <p style="font-size: 12px; color: #666; font-style: italic;">No hay archivos cargados en este ticket
                        para adjuntar.</p>
                @endforelse
            </div>
        </div>

        <div class="g_margin_bottom_10">
            <label><i class="fa-solid fa-cloud-arrow-up"></i> Cargar nuevos adjuntos:</label>
            <input type="file" wire:model="nuevosArchivos" multiple class="g_boton g_boton_light"
                style="width: 100%; border: 1px dashed #ccc; padding: 10px;">
            <div wire:loading wire:target="nuevosArchivos" class="g_negrita"
                style="color: var(--color-primary); font-size: 12px; margin-top: 5px;">
                <i class="fa-solid fa-spinner fa-spin"></i> Procesando archivos...
            </div>

            @if($nuevosArchivos)
                <div style="margin-top: 10px; font-size: 12px; color: #555;">
                    <strong>Archivos seleccionados:</strong>
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        @foreach($nuevosArchivos as $tempFile)
                            <li>{{ $tempFile->getClientOriginalName() }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @error('nuevosArchivos.*') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="formulario_botones">
            <button class="g_boton g_boton_guardar" style="width: 100%; justify-content: center;" wire:click="enviar"
                wire:loading.attr="disabled" @if(!$ticket->email) disabled @endif>
                <span wire:loading.remove wire:target="enviar">
                    <i class="fa-solid fa-paper-plane"></i> Enviar Correo ahora
                </span>
                <span wire:loading wire:target="enviar">
                    <i class="fa-solid fa-spinner fa-spin"></i> Procesando envío...
                </span>
            </button>
        </div>
    </div>
</div>