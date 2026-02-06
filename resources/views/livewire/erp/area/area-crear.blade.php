<div class="g_gap_pagina">

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Área</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
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
                            <label for="nombre">Nombre del Área <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="email_buzon">Email Buzón (ATC)</label>
                            <input type="email" id="email_buzon" wire:model.blur="email_buzon"
                                class="@error('email_buzon') input-error @enderror" autocomplete="off">
                            @error('email_buzon')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="color">Color Representativo</label>
                            <input type="color" id="color" wire:model.blur="color"
                                class="@error('color') input-error @enderror">
                            @error('color')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="icono">Icono (FontAwesome)</label>
                            <input type="text" id="icono" wire:model.blur="icono"
                                class="@error('icono') input-error @enderror" autocomplete="off">
                            @error('icono')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label>Sedes vinculadas</label>
                        <div>
                            @foreach($sedes as $sede)
                                <label>
                                    <input type="checkbox" wire:model="selectedSedes" value="{{ $sede->id }}">
                                    <span>{{ $sede->nombre }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedSedes')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
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

                        <a href="{{ route('erp.area.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>