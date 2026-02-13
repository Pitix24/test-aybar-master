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

            <div class="g_margin_bottom_20">
                <label><i class="fa-solid fa-paperclip"></i> Archivos Adjuntos:</label>

                <input type="file" id="emailFileUpload" wire:model="nuevosArchivos" multiple style="display: none;">

                <div class="contenedor_dropzone"
                    onclick="if(event.target.closest('.dropzone_remove_button')) return; document.getElementById('emailFileUpload').click()">
                    @if($nuevosArchivos)
                        <div style="width: 100%; display: grid; gap: 10px; margin-bottom: 10px;">
                            @foreach($nuevosArchivos as $index => $tempFile)
                                <div class="dropzone_item">
                                    @php
                                        $ext = strtolower($tempFile->getClientOriginalExtension());
                                        $icon = match ($ext) {
                                            'pdf' => 'fa-file-pdf',
                                            'docx', 'doc' => 'fa-file-word',
                                            'xlsx', 'xls' => 'fa-file-excel',
                                            'jpg', 'jpeg', 'png' => 'fa-file-image',
                                            default => 'fa-file'
                                        };
                                    @endphp
                                    <i class="fa-solid {{ $icon }}"
                                        style="font-size: 1.2rem !important; color: var(--primary);"></i>
                                    <span
                                        title="{{ $tempFile->getClientOriginalName() }}">{{ $tempFile->getClientOriginalName() }}</span>
                                    <button type="button" wire:click.stop="quitarArchivo({{ $index }})"
                                        class="dropzone_remove_button">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <p style="font-size: 12px; color: var(--primary); font-weight: 600;"><i
                                class="fa-solid fa-plus"></i> Añadir más archivos</p>
                    @else
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p>Haz clic para cargar adjuntos</p>
                        <small style="color: #94a3b8;">Formatos permitidos: PDF, DOCX, XLSX, JPG, PNG (Máx. 10MB)</small>
                    @endif

                    <div wire:loading wire:target="nuevosArchivos" class="g_resaltado info"
                        style="width: 100%; margin-top: 10px;">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>Procesando archivos seleccionados...</span>
                    </div>
                </div>

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