<form wire:submit.prevent="updateProspecto" class="formulario">
    <div class="g_fila">
        <div class="g_margin_bottom_10 g_columna_6">
            <label>Nombres Completos <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model="nombres" class="@error('nombres') input-error @enderror">
            @error('nombres') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
        <div class="g_margin_bottom_10 g_columna_6">
            <label>DNI / Documento <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model="dni" class="@error('dni') input-error @enderror" readonly>
            @error('dni') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="g_fila">
        <div class="g_margin_bottom_10 g_columna_6">
            <label>Correo Electrónico <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="email" wire:model="email" class="@error('email') input-error @enderror">
            @error('email') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
        <div class="g_margin_bottom_10 g_columna_6">
            <label>Celular <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model="celular" class="@error('celular') input-error @enderror">
            @error('celular') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="g_fila">
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Proyecto <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <select wire:model="proyecto_id" class="@error('proyecto_id') select-error @enderror">
                <option value="">Seleccione...</option>
                @foreach ($proyectos as $p)
                    <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
            @error('proyecto_id') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Manzana</label>
            <input type="text" wire:model="manzana">
        </div>
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Lote</label>
            <input type="text" wire:model="lote">
        </div>
    </div>

    <div class="g_tab_form_buttons">
        <button type="submit" class="g_boton guardar">
            <i class="fa-solid fa-save"></i> Actualizar Datos Personales
        </button>
    </div>
</form>