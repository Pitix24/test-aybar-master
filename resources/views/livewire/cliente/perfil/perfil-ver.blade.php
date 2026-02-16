<div class="g_panel">
    @if (session()->has('success'))
        <div class="g_alerta success g_margin_bottom_10">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta error g_margin_bottom_10">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="g_alerta info g_margin_bottom_10">
            <i class="fa-solid fa-circle-info"></i>
            {{ session('info') }}
        </div>
    @endif

    <div class="g_panel_titulo">
        <h2>
            {{ $cliente->user && $cliente->user->name
    ? 'Bienvenido, ' . collect(explode(' ', trim($cliente->user->name)))->last() . '!'
    : 'Mi perfil' }}
        </h2>
    </div>

    <form wire:submit.prevent="actualizarDatos" class="formulario">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_12">
                <label>Nombres y apellidos</label>
                <input type="text" disabled value="{{ $cliente->user->name ?? 'Sin asignar' }}">
            </div>
        </div>

        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_4">
                <label for="dni">DNI</label>
                <input type="text" id="dni" value="{{ $cliente->dni }}" autocomplete="off" readonly disabled>
            </div>

            <div class="g_margin_bottom_10 g_columna_4">
                <label for="email">Email</label>
                <input type="email" id="email" value="{{ $cliente->email }}" autocomplete="email" readonly disabled>
            </div>

            <div class="g_margin_bottom_10 g_columna_4">
                <label for="telefono_principal">Celular</label>
                <input type="text" id="telefono_principal" wire:model.blur="telefono_principal"
                    class="@error('telefono_principal') input-error @enderror" autocomplete="tel">
                @error('telefono_principal')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="formulario_botones">
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled" wire:target="actualizarDatos">
                <span wire:loading.remove wire:target="actualizarDatos">
                    Confirmar Celular
                </span>
                <span wire:loading wire:target="actualizarDatos">
                    <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                </span>
            </button>
        </div>
    </form>
</div>