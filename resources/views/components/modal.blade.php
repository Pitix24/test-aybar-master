@props([
    'open' => false,
    'maxWidth' => null,
])
<div x-data="{ open: @js($open) }" x-show="open" x-cloak @keydown.escape.window="open = false" class="g_modal">
    <div class="modal_contenedor" @click.stop @if ($maxWidth) style="max-width: {{ $maxWidth }};" @endif>
        <div class="modal_cerrar">
            <button type="button" @click="open = false">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal_titulo">
            {{ $titulo ?? '' }}
        </div>

        <div class="modal_cuerpo">
            {{ $cuerpo ?? '' }}
        </div>

        <br>

        <div class="formulario_botones g_centrar_elemento">
            <button type="button" @click="open = false" class="guardar">
                ACEPTAR
            </button>
        </div>
    </div>
</div>