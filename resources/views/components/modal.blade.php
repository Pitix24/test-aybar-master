@props([
    'id' => null,
    'maxWidth' => '550px',
    'title' => null,
    'wireClose' => null, // Acción de Livewire al cerrar
])

<div id="{{ $id }}" class="g_modal" x-data="{ open: true }" x-show="open" x-cloak
    @keydown.escape.window="{{ $wireClose ? '$wire.' . $wireClose . '()' : 'open = false' }}">

    <div class="modal_contenedor" @click.stop style="max-width: {{ $maxWidth }};">
        <!-- Botón Cerrar -->
        <div class="modal_cerrar">
            @if ($wireClose)
                <button type="button" wire:click="{{ $wireClose }}" title="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            @else
                <button type="button" @click="open = false" title="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            @endif
        </div>

        <!-- Título -->
        @if ($title || isset($titulo))
            <div class="modal_titulo">
                <h2>{{ $title ?? $titulo }}</h2>
            </div>
        @endif

        <!-- Cuerpo -->
        <div class="modal_cuerpo">
            {{ $slot ?? ($cuerpo ?? '') }}
        </div>

        <!-- Pie / Botones -->
        @if (isset($pie))
            <div class="modal_pie">
                {{ $pie }}
            </div>
        @elseif(isset($footer))
            <div class="modal_pie">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>