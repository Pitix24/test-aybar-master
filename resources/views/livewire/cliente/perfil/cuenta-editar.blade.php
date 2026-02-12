<div class="g_panel">
    @if (session()->has('success'))
        <div class="g_alerta_succes">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
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
                <input type="password" wire:model="clave_actual" name="clave_actual" id="clave_actual">
                @error('clave_actual')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="g_margin_top_20 g_columna_6">
                <label for="clave_nueva">Nueva contraseña <span class="obligatorio"><i
                            class="fa-solid fa-asterisk"></i></span></label>
                <input type="password" wire:model="clave_nueva" name="clave_nueva" id="clave_nueva">
                @error('clave_nueva')
                    <span class="mensaje_error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="g_margin_top_20 formulario_botones">
            <button type="submit" class="guardar">Cambiar contraseña</button>
        </div>
    </form>
</div>