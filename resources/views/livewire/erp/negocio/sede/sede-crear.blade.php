<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Sede</h2>

        <div class="cabecera_titulo_botones">
            @can('sede.lista')
                <a href="{{ route('erp.sede.vista.todo') }}" class="g_boton light">
                    Lista <i class="fa-solid fa-list"></i>
                </a>
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
                                {{ $activo ? 'Activo' : 'Desactivado' }}
                            </span>

                            @error('activo')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="nombre">
                            Nombre <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" id="nombre" wire:model.blur="nombre"
                            class="@error('nombre') input-error @enderror" autocomplete="off">
                        @error('nombre')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="direccion">
                            Dirección
                        </label>
                        <textarea id="direccion" wire:model.blur="direccion"
                            class="@error('direccion') input-error @enderror" rows="3"></textarea>
                        @error('direccion')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="formulario_botones">
                        @can('sede.crear')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-save"></i> Crear
                                </span>
                                <span wire:loading wire:target="store">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                                </span>
                            </button>
                        @endcan

                        @can('sede.lista')
                            <button type="button" class="g_boton cancelar" onclick="history.back()">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>