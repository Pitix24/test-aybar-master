@section('tituloPagina', 'Crear Prioridad de Ticket')

<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Prioridad</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.prioridad-ticket.vista.todo') }}" class="g_boton g_boton_light">
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

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="nombre">Nombre <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="tiempo_permitido">Tiempo Permitido (Horas) <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="number" id="tiempo_permitido" wire:model.blur="tiempo_permitido"
                                class="@error('tiempo_permitido') input-error @enderror" autocomplete="off">
                            @error('tiempo_permitido')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="color">Color</label>
                            <input type="color" id="color" wire:model.blur="color"
                                class="@error('color') input-error @enderror" style="height: 40px; padding: 2px;">
                            @error('color')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="icono">Icono (FontAwesome)</label>
                            <input type="text" id="icono" wire:model.blur="icono" placeholder="fa-solid fa-flag"
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

                        <a href="{{ route('erp.prioridad-ticket.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
            <div class="g_columna_4">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Previsualización</h4>
                    <div
                        style="display: flex; align-items: center; gap: 15px; padding: 20px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0;">
                        <i class="{{ $icono ?: 'fa-solid fa-flag' }}"
                            style="font-size: 2rem; color: {{ $color ?: '#3b82f6' }}"></i>
                        <div>
                            <h3 style="margin: 0; color: #1e293b;">{{ $nombre ?: 'Nombre Prioridad' }}</h3>
                            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">
                                {{ $tiempo_permitido ?: '0' }} Horas permitidas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>