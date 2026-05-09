<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="update, eliminarPrioridadSoporteOn" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Editar Prioridad de Soporte</h2>

        <div class="cabecera_titulo_botones">
            @can('soporte.supervisor')
            <a href="{{ route('erp.prioridad-soporte.vista.lista') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i></a>
            @endcan

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="update" class="formulario">
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
                            Nombre de la Prioridad <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
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
                            <p class="leyenda">Ej: fa-solid fa-circle-down, fa-solid fa-triangle-exclamation</p>
                        </div>
                    </div>

                    <div class="formulario_botones">
                        @can('prioridad-soporte.accion-editar')
                        <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="update">
                            <span wire:loading.remove wire:target="update">
                                <i class="fa-solid fa-save"></i> Guardar
                            </span>
                            <span wire:loading wire:target="update">
                                <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                            </span>
                        </button>
                        @endcan

                        @can('prioridad-soporte.accion-eliminar')
                        <button type="button" class="g_boton eliminar"
                            onclick="if(confirm('¿Está seguro que desea eliminar esta prioridad de soporte?')) { Livewire.dispatch('eliminarPrioridadSoporteOn'); }"
                            wire:loading.attr="disabled" wire:target="eliminarPrioridadSoporteOn">
                            <span wire:loading.remove wire:target="eliminarPrioridadSoporteOn">
                                <i class="fa-solid fa-trash"></i> Eliminar
                            </span>
                            <span wire:loading wire:target="eliminarPrioridadSoporteOn">
                                <i class="fa-solid fa-spinner fa-spin"></i> Eliminando...
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