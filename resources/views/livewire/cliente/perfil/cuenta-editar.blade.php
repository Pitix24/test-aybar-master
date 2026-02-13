<div class="g_panel">
    @if (session()->has('success'))
        <div class="g_alerta success g_margin_bottom_20">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="g_alerta error g_margin_bottom_20">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('info'))
        <div class="g_alerta info g_margin_bottom_20">
            <i class="fa-solid fa-circle-info"></i>
            {{ session('info') }}
        </div>
    @endif

    <div class="g_panel_titulo">
        <h2>Cambiar contraseña</h2>
    </div>

    <form wire:submit.prevent="actualizarClave" class="formulario">
        <div class="g_fila">
            <div class="g_margin_top_20 g_columna_6">
                <label for="clave_actual">Contraseña actual <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="password" wire:model.blur="clave_actual" name="clave_actual" id="clave_actual"
                    class="@error('clave_actual') input-error @enderror" autocomplete="current-password">
                @error('clave_actual')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>

            <div class="g_margin_top_20 g_columna_6">
                <label for="clave_nueva">Nueva contraseña <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="password" wire:model.blur="clave_nueva" name="clave_nueva" id="clave_nueva"
                    class="@error('clave_nueva') input-error @enderror" autocomplete="new-password">
                @error('clave_nueva')
                    <p class="mensaje_error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="g_margin_top_20 formulario_botones">
            <button type="submit" class="g_boton g_boton_guardar" wire:loading.attr="disabled"
                wire:target="actualizarClave">
                <span wire:loading.remove wire:target="actualizarClave">
                    Cambiar contraseña
                </span>
                <span wire:loading wire:target="actualizarClave">
                    <i class="fa-solid fa-spinner fa-spin"></i> Actualizando...
                </span>
            </button>
        </div>
    </form>
</div>