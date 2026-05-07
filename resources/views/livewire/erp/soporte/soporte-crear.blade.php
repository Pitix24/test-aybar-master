<div class="g_gap_pagina">
    <x-loading-overlay wire:loading wire:target="guardar" message="Guardando cambios..." />

    <div class="g_panel cabecera_titulo_pagina">
        <h2>Crear Ticket de Soporte</h2>

        <div class="cabecera_titulo_botones">
            <a href="{{ route('erp.soporte.vista.todo') }}" class="g_boton light">
                Lista <i class="fa-solid fa-list"></i>
            </a>

            <button type="button" class="g_boton dark" onclick="history.back()">
                <i class="fa-solid fa-arrow-left"></i> Regresar
            </button>
        </div>
    </div>

    <form wire:submit="guardar" class="formulario g_panel">
        <div class="g_fila">
            <div class="g_margin_bottom_10 g_columna_3">
                <label>Tipo <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="tipo_soporte_id" class="@error('tipo_soporte_id') input-error @enderror">
                    <option value="">Seleccione...</option>
                    @foreach ($tipos as $t)
                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @error('tipo_soporte_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Prioridad <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
                <select wire:model.live="prioridad_soporte_id"
                    class="@error('prioridad_soporte_id') input-error @enderror">
                    <option value="">Seleccione...</option>
                    @foreach ($prioridades as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                    @endforeach
                </select>
                @error('prioridad_soporte_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>

            <div class="g_margin_bottom_10 g_columna_3">
                <label>Área Asignada</label>
                <select wire:model.live="area_id" class="@error('area_id') input-error @enderror">
                    <option value="">Sin área asignada</option>
                    @foreach ($areas as $a)
                        <option value="{{ $a->id }}">{{ $a->nombre }}</option>
                    @endforeach
                </select>
                @error('area_id') <p class="mensaje_error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="g_margin_bottom_10">
            <label>Título <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="titulo" class="@error('titulo') input-error @enderror">
            @error('titulo') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_margin_bottom_10">
            <label>Descripción <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <textarea wire:model.blur="descripcion" rows="6"
                class="@error('descripcion') input-error @enderror"></textarea>
            @error('descripcion') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="formulario_botones">
            <button type="submit" class="g_boton guardar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="guardar">
                    <i class="fa-solid fa-save"></i> Guardar
                </span>
                <span wire:loading wire:target="guardar">
                    <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                </span>
            </button>

            <button type="button" class="g_boton cancelar" onclick="history.back()">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        </div>
    </form>
</div>
