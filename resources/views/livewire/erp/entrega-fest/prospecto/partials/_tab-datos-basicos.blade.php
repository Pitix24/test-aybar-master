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

    <div class="g_fila">
        <div class="g_margin_bottom_10 g_columna_12">
            <label>Estado del Cliente <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <select wire:model="estado_cliente_id" class="@error('estado_cliente_id') select-error @enderror">
                <option value="">Seleccione...</option>
                @foreach ($estados_cliente as $ec)
                <option value="{{ $ec->id }}">{{ $ec->nombre }}</option>
                @endforeach
            </select>
            @error('estado_cliente_id') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="g_tab_form_buttons">
        @can('prospecto.editar')
        <button type="submit" class="g_boton guardar">
            <i class="fa-solid fa-save"></i> Actualizar Datos Personales
        </button>
        @endcan
    </div>
</form>

<div class="g_panel_seccion_divider" style="margin-top:12px"></div>
<form wire:submit.prevent="updateReubicacion" class="formulario">
    <h4 class="g_negrita" style="margin-bottom:8px">Reubicación</h4>

    <div class="g_fila">
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Proyecto Reubicado</label>
            <select wire:model="reubicado_proyecto_id">
                <option value="">Seleccione...</option>
                @foreach ($proyectos as $p)
                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Manzana Reubicada</label>
            <input type="text" wire:model="reubicado_manzana">
        </div>
        <div class="g_margin_bottom_10 g_columna_4">
            <label>Lote Reubicado</label>
            <input type="text" wire:model="reubicado_lote">
        </div>
    </div>

    <div class="g_tab_form_buttons">
        @can('prospecto.editar')
        <button type="submit" class="g_boton info">
            <i class="fa-solid fa-location-dot"></i> Registrar Reubicación
        </button>
        @endcan
    </div>
</form>
