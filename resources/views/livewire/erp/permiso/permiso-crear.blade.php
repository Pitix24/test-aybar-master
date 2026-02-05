@section('tituloPagina', 'Crear Permiso')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Permiso</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.permiso.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i></a>

            <button type="button" class="g_boton g_boton_dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar</button>
        </div>
    </div>

    <form wire:submit="store" class="formulario">
        <div class="g_panel">
            <h4 class="g_panel_titulo">General</h4>

            <div class="g_fila">
                <div class="g_columna_6 g_margin_bottom_10">
                    <label for="name">
                        Nombre del Permiso <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                    </label>
                    <input type="text" id="name" wire:model.blur="name" class="@error('name') input-error @enderror"
                        autocomplete="off"">
                        @error('name')
                            <p class=" mensaje_error">{{ $message }}</p>
                        @enderror
                    <p class="leyenda">Ej: rol-editar</p>
                </div>

                <div class="g_columna_6 g_margin_bottom_10">
                    <label for="module">
                        Módulo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                    </label>
                    <input type="text" id="module" wire:model.blur="module"
                        class="@error('module') input-error @enderror" autocomplete="off">
                    @error('module')
                        <p class="mensaje_error">{{ $message }}</p>
                    @enderror
                    <p class="leyenda">Ej: Atención al Cliente</p>
                </div>
            </div>

            <div class="formulario_botones">
                <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled" wire:target="store">
                    <span wire:loading.remove wire:target="store">
                        <i class="fa-solid fa-save"></i> Guardar
                    </span>
                    <span wire:loading wire:target="store">
                        <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                    </span>
                </button>

                <a href="{{ route('erp.permiso.vista.todo') }}" class="g_boton g_boton_cancelar">
                    <i class="fa-solid fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </form>
</div>