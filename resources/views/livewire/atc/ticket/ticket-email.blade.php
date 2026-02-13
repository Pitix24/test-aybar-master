<div class="g_panel" x-data="{ activeTab: 'enviar' }">

    <div class="g_tab_navegacion">
        <div class="g_tab_botones">
            <button type="button" @click="activeTab = 'enviar'"
                :class="activeTab === 'enviar' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                <i class="fa-solid fa-paper-plane"></i> Enviar Email al Cliente
            </button>

            <button type="button" @click="activeTab = 'historial'"
                :class="activeTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                <i class="fa-solid fa-clock-rotate-left"></i> Historial de Correos
            </button>
        </div>
    </div>


    <div x-show="activeTab === 'enviar'" x-transition class="g_tab_content">
        <div class="formulario">
            @if(!$ticket->email)
                <p class="g_resaltado error">
                    <i class="fa-solid fa-info-circle"></i>
                    <span>
                        El cliente no tiene un email registrado en este ticket. No se puede realizar el envío.
                    </span>
                </p>
            @endif

            <div class="g_margin_bottom_10">
                <label>Destinatario:</label>
                <input type="text" value="{{ $ticket->email }}" disabled class="g_input_disabled">
            </div>

            <div class="g_margin_bottom_10">
                <label>Asunto:</label>
                <input type="text" wire:model="asunto">
                @error('asunto') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10">
                <label>Mensaje:</label>
                <textarea wire:model="mensaje" rows="6"></textarea>
                @error('mensaje') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10">
                <label><i class="fa-solid fa-cloud-arrow-up"></i> Cargar nuevos adjuntos para el correo:</label>
                <input type="file" wire:model="nuevosArchivos" multiple class="g_boton g_boton_light"
                    style="width: 100%; border: 1px dashed #ccc; padding: 10px;">
                <div wire:loading wire:target="nuevosArchivos" class="g_negrita"
                    style="color: var(--color-primary); font-size: 12px; margin-top: 5px;">
                    <i class="fa-solid fa-spinner fa-spin"></i> Procesando archivos...
                </div>

                @if($nuevosArchivos)
                    <div style="margin-top: 10px; font-size: 12px; color: #555;">
                        <strong>Archivos listos para enviar:</strong>
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
                <button class="g_boton g_boton_guardar" wire:click="enviar" wire:loading.attr="disabled"
                    @if(!$ticket->email) disabled @endif>
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

    <div x-show="activeTab === 'historial'" x-transition class="g_tab_content">
        <div class="g_contenedor_tabla">
            <table class="g_tabla">
                <thead>
                    <tr>
                        <th>Fecha Envío</th>
                        <th>Gestor</th>
                        <th>Asunto</th>
                        <th>Mensaje Corto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($correos as $cor)
                        <tr>
                            <td class="g_negrita g_inferior">{{ $cor->enviado_at->format('d/m H:i') }}</td>
                            <td><small>{{ $cor->emisor->name ?? 'Sistema' }}</small></td>
                            <td><span class="g_badge g_badge_light" style="font-size: 10px;">{{ $cor->asunto }}</span></td>
                            <td class="g_resumir" title="{{ $cor->mensaje }}">{{ $cor->mensaje }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="g_vacio">
                                    <i class="fa-regular fa-face-grin-wink"></i>
                                    <p>No hay registros de correos enviados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>