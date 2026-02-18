<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Estado de Ticket</h2>

        <div class="cabecera_titulo_botones">
            @can('estado-ticket.lista')
                <a href="{{ route('erp.estado-ticket.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_8">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

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
                                {{ $activo ? 'Activo' : 'Inactivo' }}
                            </span>

                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="nombre">
                            Nombre del Estado <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" id="nombre" wire:model.blur="nombre"
                            class="@error('nombre') input-error @enderror" autocomplete="off">
                        @error('nombre')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_fila">
                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="color">
                                Color Informativo
                            </label>
                            <input type="color" id="color" wire:model.blur="color"
                                class="@error('color') input-error @enderror">
                            @error('color')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_columna_6 g_margin_bottom_10">
                            <label for="icono">
                                Icono (FontAwesome)
                            </label>
                            <input type="text" id="icono" wire:model.blur="icono" placeholder="fa-solid fa-circle-info"
                                class="@error('icono') input-error @enderror" autocomplete="off">
                            @error('icono')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                            <p class="leyenda">Ej: fa-solid fa-circle-check, fa-solid fa-spinner</p>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        @can('estado-ticket.crear')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-save"></i> Crear
                                </span>
                                <span wire:loading wire:target="store">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                                </span>
                            </button>
                        @endcan

                        <button type="button" class="g_boton cancelar" onclick="history.back()">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>