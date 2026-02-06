@section('tituloPagina', 'Crear Estado de Ticket')

<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Nuevo Estado</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.estado-ticket.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">General</h4>

                    <div class="g_margin_bottom_10">
                        <label for="estado_activo">
                            Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>

                        <div class="g_switch-wrapper">
                            <label class="g_switch">
                                <input id="estado_activo" type="checkbox" wire:model.live="activo">
                                <span class="g_switch-slider"></span>
                            </label>

                            <span class="g_switch-label">
                                {{ $activo ? 'Activo' : 'Desactivado' }}
                            </span>

                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="nombre">Nombre del Estado <span class="obligatorio"><i
                                    class="fa-solid fa-asterisk"></i></span></label>
                        <input type="text" id="nombre" wire:model.blur="nombre"
                            placeholder="Ej: Pendiente, En proceso, Cerrado"
                            class="@error('nombre') input-error @enderror" autocomplete="off">
                        @error('nombre')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="color">Color Representativo</label>
                            <input type="color" id="color" wire:model.blur="color"
                                class="@error('color') input-error @enderror" style="height: 40px; padding: 2px;">
                            @error('color')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="icono">Icono (FontAwesome)</label>
                            <input type="text" id="icono" wire:model.blur="icono" placeholder="fa-solid fa-check-circle"
                                class="@error('icono') input-error @enderror" autocomplete="off">
                            @error('icono')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                            wire:target="store">
                            <span wire:loading.remove wire:target="store">
                                <i class="fa-solid fa-save"></i> Guardar
                            </span>
                            <span wire:loading wire:target="store">
                                <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                            </span>
                        </button>

                        <a href="{{ route('erp.estado-ticket.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Previsualización</h4>
                    <div
                        style="padding: 30px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; flex-direction: column; align-items: center; gap: 20px;">
                        <i class="{{ $icono ?: 'fa-solid fa-circle' }}"
                            style="font-size: 3rem; color: {{ $color ?: '#64748b' }}"></i>

                        <div style="text-align: center;">
                            <span
                                style="background-color: {{ $color ?: '#64748b' }}; color: white; padding: 6px 16px; border-radius: 20px; font-weight: 500; font-size: 1.1rem; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                {{ $nombre ?: 'Nombre Estado' }}
                            </span>
                        </div>

                        <p style="color: #64748b; font-size: 0.85rem; text-align: center; margin: 0;">
                            Así es como se verá el estado en las etiquetas y resúmenes del ticket.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>