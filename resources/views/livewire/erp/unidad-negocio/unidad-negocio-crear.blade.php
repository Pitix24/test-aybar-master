@section('tituloPagina', 'Crear unidad negocio')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear unidad negocio</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i>
            </a>

            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_darkt">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>

    <form wire:submit.prevent="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_12">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">General</h4>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="nombre">Nombre <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="nombre" wire:model.live="nombre">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="razon_social">Razon social <span class="obligatorio"><i
                                        class="fa-solid fa-asterisk"></i></span></label>
                            <input type="text" id="razon_social" wire:model.live="razon_social">
                            @error('razon_social')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_empresa_primario" wire:loading.attr="disabled"
                            wire:target="store">
                            <span wire:loading.remove wire:target="store">Crear</span>
                            <span wire:loading wire:target="store">Creando...</span>
                        </button>

                        <a href="{{ route('erp.unidad-negocio.vista.todo') }}"
                            class="g_boton g_boton_empresa_secundario">
                            Cancelar </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>