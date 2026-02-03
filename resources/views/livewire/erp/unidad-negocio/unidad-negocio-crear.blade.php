@section('tituloPagina', 'Crear unidad negocio')

<div class="g_gap_pagina">
    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear unidad negocio</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_light">
                Inicio <i class="fa-solid fa-house"></i>
            </a>

            <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_dark">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="g_alerta_succes">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta_error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="store" class="formulario">
        <div class="g_fila">
            <div class="g_columna_12">
                <div class="g_panel">
                    <h4 class="g_panel_titulo">Información General</h4>

                    <div class="g_fila">
                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="nombre">
                                Nombre <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="nombre" wire:model.blur="nombre"
                                class="@error('nombre') input-error @enderror" autocomplete="off">
                            @error('nombre')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="g_margin_bottom_10 g_columna_6">
                            <label for="razon_social">
                                Razón social <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span>
                            </label>
                            <input type="text" id="razon_social" wire:model.blur="razon_social"
                                class="@error('razon_social') input-error @enderror" autocomplete="off">
                            @error('razon_social')
                                <p class="mensaje_error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="formulario_botones">
                        <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                            wire:target="store">
                            <span wire:loading.remove wire:target="store">
                                <i class="fa-solid fa-save"></i> Crear
                            </span>
                            <span wire:loading wire:target="store">
                                <i class="fa-solid fa-spinner fa-spin"></i> Creando...
                            </span>
                        </button>

                        <a href="{{ route('erp.unidad-negocio.vista.todo') }}" class="g_boton g_boton_cancelar">
                            <i class="fa-solid fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>