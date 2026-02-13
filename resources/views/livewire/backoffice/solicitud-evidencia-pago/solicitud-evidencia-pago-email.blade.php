<div class="g_panel" x-data="{ activeTab: 'enviar' }">
    <div class="g_tab_navegacion">
        <div class="g_tab_botones">
            <button type="button" @click="activeTab = 'enviar'"
                :class="activeTab === 'enviar' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                <i class="fa-solid fa-paper-plane"></i> Enviar Observación
            </button>

            <button type="button" @click="activeTab = 'enviar_y_cambiar_estado'"
                :class="activeTab === 'enviar_y_cambiar_estado' ? 'g_tab_active' : 'g_tab_inactive'"
                class="g_tab_boton">
                <i class="fa-solid fa-triangle-exclamation"></i> Enviar y Rechazar
            </button>

            <button type="button" @click="activeTab = 'historial'"
                :class="activeTab === 'historial' ? 'g_tab_active' : 'g_tab_inactive'" class="g_tab_boton">
                <i class="fa-solid fa-clock-rotate-left"></i> Historial de Correos
            </button>
        </div>
    </div>

    <div x-show="activeTab === 'enviar'" x-transition class="g_tab_content">
        <div class="formulario">
            <p class="g_resaltado_info" style="font-size: 13px; margin-bottom: 10px;">
                <i class="fa-solid fa-info-circle"></i> Esta opción solo envía el correo informativo al cliente,
                <strong>no cambia</strong> el estado de la solicitud.
            </p>
            <div class="g_margin_bottom_10">
                <label>Mensaje para el cliente</label>
                <textarea wire:model.live="mensaje_correo" rows="5"
                    placeholder="Escribe el mensaje informativo..."></textarea>
                @error('mensaje_correo') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
            <div class="formulario_botones">
                <button type="button" wire:click="enviarCorreo(false)" class="g_boton g_boton_primary">
                    Enviar Solo Correo <i class="fa-solid fa-envelope"></i>
                </button>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'enviar_y_cambiar_estado'" x-transition class="g_tab_content">
        <div class="formulario">
            @if($evidenciaSeleccionada)
                <div class="g_alerta_warning" style="margin-bottom: 15px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <strong>Atención:</strong> Se enviará el correo y se marcará como <strong>RECHAZADA</strong> la
                    evidencia #{{ $evidenciaSeleccionada->numero_operacion }} y la solicitud entera.
                </div>
                <div class="g_margin_bottom_10">
                    <label>Motivo del Rechazo (mensaje para el cliente)</label>
                    <textarea wire:model.live="mensaje_correo" rows="5"
                        placeholder="Escribe detalladamente por qué se rechaza el pago..."></textarea>
                    @error('mensaje_correo') <p class="mensaje_error">{{ $message }}</p> @enderror
                </div>
                <div class="formulario_botones">
                    <button type="button" wire:click="enviarCorreo(true)" class="g_boton g_boton_danger">
                        Enviar y Rechazar Solicitud <i class="fa-solid fa-ban"></i>
                    </button>
                </div>
            @else
                <div class="g_vacio" style="padding: 20px;">
                    <i class="fa-solid fa-hand-pointer fa-bounce"></i>
                    <p>Debe seleccionar una evidencia de la lista de la izquierda para poder usar esta funcionalidad.</p>
                </div>
            @endif
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
                            <td colspan="4" class="g_celda_vacia">No hay registros de correos enviados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>