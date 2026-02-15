<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="store" message="Procesando..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Permiso</h2>

        <div class="cabecera_titulo_botones">
            @can('permiso.ver')
                <a href="{{ route('erp.permiso.vista.todo') }}" class="g_boton light">
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
                    <h4 class="g_panel_titulo">General</h4>

                    <div class="g_margin_bottom_10">
                        <label for="name">
                            Nombre del Permiso <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" id="name" wire:model.blur="name" class="@error('name') input-error @enderror"
                            autocomplete="off">
                        @error('name')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                        <p class="leyenda">Ej: rol.editar (Usar punto para separar recurso de acción)</p>
                    </div>

                    <div class="g_margin_bottom_10">
                        <label for="module">
                            Módulo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                        </label>
                        <input type="text" id="module" wire:model.blur="module"
                            class="@error('module') input-error @enderror" autocomplete="off">
                        @error('module')
                            <p class="mensaje_error">{{ $message }}</p>
                        @enderror
                        <p class="leyenda">Ej: Sistema</p>
                    </div>

                    <div class="formulario_botones">
                        @can('permiso.crear')
                            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">
                                    <i class="fa-solid fa-save"></i> Crear
                                </span>
                                <span wire:loading wire:target="store">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                                </span>
                            </button>
                        @endcan

                        @can('permiso.ver')
                            <a href="{{ route('erp.permiso.vista.todo') }}" class="g_boton cancelar">
                                <i class="fa-solid fa-times"></i> Cancelar
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>